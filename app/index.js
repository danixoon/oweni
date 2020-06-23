const Tesseract = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");
const express = require("express");
const bodyParser = require("body-parser");
const multer = require("multer");

const upload = multer({ storage: multer.memoryStorage() });

// /process.env.TESSDATA_PREFIX = path.resolve(__dirname);

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
      // const c = char.charCodeAt(0);
      // const ok = c === " " || (c >= 1024 && c <= 1279) || (c >= 48 && c <= 57);
      return true;
    })
    .join("");

const schemas = {
  [SCHEMA_TYPE.RECRUIT_CASE]: [
    {
      name: "last_name",
      bounds: [71, 47, 620, 71],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "first_name",
      bounds: [71, 72, 618, 92],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "family_name",
      bounds: [71, 92, 618, 118],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "birthday",
      bounds: [118, 118, 262, 138],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "citizenship",
      bounds: [356, 119, 620, 138],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "living_address",
      bounds: [242, 156, 620, 174],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "off_address",
      bounds: [242, 184, 620, 209],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "home_phone",
      bounds: [300, 229, 420, 262],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "private_phone",
      bounds: [505, 229, 620, 262],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "position",
      bounds: [113, 271, 620, 303],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "education",
      // В случае, если тип данных таблица в bounds передаётся массив с границами столбцов
      bounds: {
        name: [0, 393, 159, 482],
        takeof: [156, 395, 296, 482],
        release: [298, 395, 431, 482],
        branch: [430, 395, 620, 482],
        // TODO: Добавить столбцы по шаблону ниже:
        // takeof: [...]
        // release: [...]
      },
      type: DATA_TYPE.TABLE,
    },
    {
      name: "languages",
      bounds: [174, 489, 620, 513],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "relative",
      // В случае, если тип данных таблица в bounds передаётся массив с границами столбцов
      bounds: {
        name: [0, 587, 286, 742],
        work_place: [287, 587, 500, 742],
        birthday: [500, 587, 620, 742],
        // TODO: Добавить столбцы по шаблону ниже:
        // takeof: [...]
        // release: [...]
      },
      type: DATA_TYPE.TABLE,
    },
    {
      name: "hobby",
      bounds: [200, 745, 620, 762],
      type: DATA_TYPE.TEXT,
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
  const imageCrop = sharp(docImage).resize({ width: 1240, height: 1754, fit: "contain" }).trim(33);

  const buffer = await imageCrop.toBuffer();
  await util.promisify(fs.rmdir)(path.resolve(__dirname, `./result/${docName}`), { recursive: true });

  const result = await Promise.all(
    schema.map(async (data) => {
      switch (data.type) {
        case DATA_TYPE.TEXT: {
          const [left, top, right, bottom] = data.bounds.map((c) => c * 2);
          const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
          saveImage(part, docName, data.name);
          const scan = await Tesseract.recognize(part, "rus", { logger: (m) => console.log(m) });

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
            const [left, top, right, bottom] = bounds[columnName].map((c) => c * 2);

            const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
            saveImage(part, docName, `${data.name}-${columnName}-${i++}`);
            const scan = await Tesseract.recognize(part, "rus", { logger: (m) => console.log(m) });
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

  saveImage(buffer, docName, "_image");

  const flattenData = result.reduce((acc, data) => ({ ...acc, [data.key]: data.value }), {});

  await util.promisify(fs.unlink)(path.resolve(__dirname, "./rus.traineddata"));

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

  app.listen(5050, () => {
    console.log("server listening");
  });
};

init();
