const Tesseract = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");
const express = require("express");
const bodyParser = require("body-parser");
const multer = require("multer");

const upload = multer({ storage: multer.memoryStorage() });

process.env.TESSDATA_PREFIX = path.resolve(__dirname, "./tessdata");

const DATA_TYPE = {
  TEXT: "string",
  TABLE: "table",
};

const SCHEMA_TYPE = {
  RECRUIT_CASE: "recruitCase",
};

const extractValue = (text) =>
  text
    .split("")
    .filter((char) => {
      const c = char.charCodeAt(0);
      const ok = c === " " || (c >= 1024 && c <= 1279) || (c >= 48 && c <= 57);
      return ok;
    })
    .join("");

const schemas = {
  [SCHEMA_TYPE.RECRUIT_CASE]: [
    {
      name: "lastName",
      bounds: [70, 46, 618, 71],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "firstName",
      bounds: [70, 72, 618, 92],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "birthday",
      bounds: [118, 128, 267, 155],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "citizenship",
      bounds: [350, 128, 620, 155],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "education",
      // В случае, если тип данных таблица в bounds передаётся массив с границами столбцов
      bounds: {
        name: [0, 400, 154, 486],
        takeof: [155, 400, 296, 486],
        release: [298, 400, 431, 486],
        branch: [430, 400, 620, 486],
        // TODO: Добавить столбцы по шаблону ниже:
        // takeof: [...]
        // release: [...]
      },
      type: DATA_TYPE.TABLE,
    },
    // TODO: Добавить полей для данных
  ],
};

const saveImage = async (buffer, schemaName, fieldName) => {
  const imagePath = path.resolve(__dirname, `./result/${schemaName}/${fieldName}.png`);
  const imageDir = path.dirname(imagePath);
  try {
    await util.promisify(fs.mkdir)(imageDir, { recursive: true });
    await util.promisify(fs.writeFile)(imagePath, buffer, "binary");
  } catch (err) {
    console.error(err);
    throw err;
  }
};

const parseDocument = async (docName, docImage) => {
  const schema = schemas[docName];
  const imageCrop = sharp(docImage)
    .resize({ width: 1240 / 2, height: 1754 / 2, fit: "contain" })
    .trim(33);

  const buffer = await imageCrop.toBuffer();
  await util.promisify(fs.rmdir)(path.resolve(__dirname, `./result/${docName}`), { recursive: true });

  // saveImage(buffer, docName, "_image");

  const result = await Promise.all(
    schema.map(async (data) => {
      switch (data.type) {
        case DATA_TYPE.TEXT: {
          const [left, top, right, bottom] = data.bounds;
          const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
          saveImage(part, docName, data.name);
          const scan = await Tesseract.recognize(part, "rus");

          return {
            key: data.name,
            value: extractValue(scan.data.text),
          };
        }
        case DATA_TYPE.TABLE: {
          const { bounds } = data;
          const columnNames = Object.keys(bounds);
          let table = [];
          let i = 0;
          for (let columnName of columnNames) {
            const [left, top, right, bottom] = bounds[columnName];

            const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
            saveImage(part, docName, `${data.name}-${columnName}-${i++}`);
            const scan = await Tesseract.recognize(part, "rus");
            table.push(
              scan.data.text
                .split("\n")
                .map((v) => extractValue(v))
                .filter((value) => value)
            );
          }

          const rows = new Array(bounds.length);
          table.forEach((column, i) => {
            column.forEach((cell, r) => {
              rows[r] = { ...(rows[r] || {}), [columnNames[i]]: cell };
            });
          });

          return {
            key: data.name,
            value: rows,
          };
        }
      }
    })
  );

  const flattenData = result.reduce((acc, data) => ({ ...acc, [data.key]: data.value }), {});

  return flattenData;
};

const init = () => {
  const app = express();

  app.use(bodyParser.json());

  app.post("/api/document/parse", upload.single("image"), async (req, res, next) => {
    const { name } = req.query;
    const { file } = req;

    if (!name) return res.status(400).send({ message: "Имя документа должно быть указано" });
    if (!schemas[name]) return res.status(400).send({ message: "Неверное имя документа" });
    try {
      const docData = await parseDocument(name, file.buffer);

      console.log(`document <${name}> parse:`, util.inspect(docData, false, null, true));
      res.send(docData);
    } catch (err) {
      res.status(500).send({ message: "Некорректное изображение" });
    }
  });

  app.listen(5005, () => {
    console.log("server listening");
  });
};

init();
