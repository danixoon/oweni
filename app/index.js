const { createWorker } = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");
const express = require("express");
const bodyParser = require("body-parser");
const multer = require("multer");
const { default: Queue } = require("p-queue");

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

      const c = char.charCodeAt(0);

      return ",.'\"-".split("").includes(c) || c === " " || (c >= 1024 && c <= 1279) || (c >= 48 && c <= 57);
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
      bounds: [71, 72, 620, 92],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "family_name",
      bounds: [71, 92, 620, 118],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "birthday",
      bounds: [120, 108, 278, 138],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "citizenship",
      bounds: [364, 112, 620, 141],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "living_address",
      bounds: [256, 150, 620, 175],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "off_address",
      bounds: [260, 187, 620, 209],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "home_phone",
      bounds: [300, 221, 425, 269],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "private_phone",
      bounds: [510, 229, 620, 269],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "position",
      bounds: [112, 270, 620, 310],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "education",
      // В случае, если тип данных таблица в bounds передаётся массив с границами столбцов
      bounds: {
        name: [0, 395, 150, 480],
        takeof: [155, 395, 310, 480],
        release: [310, 395, 439, 480],
        branch: [438, 395, 620, 480],
        // TODO: Добавить столбцы по шаблону ниже:
        // takeof: [...]
        // release: [...]
      },
      type: DATA_TYPE.TABLE,
    },
    {
      name: "languages",
      bounds: [178, 482, 620, 514],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "relative",
      // В случае, если тип данных таблица в bounds передаётся массив с границами столбцов
      bounds: {
        name: [0, 596, 286, 755],
        work_place: [219, 596, 535, 755],
        birthday: [535, 596, 620, 755],
        // TODO: Добавить столбцы по шаблону ниже:
        // takeof: [...]
        // release: [...]
      },
      type: DATA_TYPE.TABLE,
    },
    {
      name: "hobby",
      bounds: [217, 755, 620, 775],
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

const parsingQueue = new Queue({ concurrency: 4 });

const parseDocument = async (docName, docImage) => {

  const clearThis = async () => {
    const trainedDataPath = path.resolve(__dirname, "./rus.traineddata");
    if (fs.existsSync(trainedDataPath))
      await util.promisify(fs.unlink)(trainedDataPath);
  }

  const schema = schemas[docName];
  const imageCrop = sharp(docImage).resize({ width: 1240, height: 1754, fit: "contain" }).trim(33);

  const buffer = await imageCrop.toBuffer();
  await util.promisify(fs.rmdir)(path.resolve(__dirname, `./result/${docName}`), { recursive: true });

  // await clearThis();

  const result = await Promise.all(schema.map(async (data) => {
    switch (data.type) {
      case DATA_TYPE.TEXT: {
        const [left, top, right, bottom] = data.bounds.map((c) => c * 2);
        const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
        saveImage(part, docName, data.name);
        const scan = await parsingQueue.add(async () => {
          const worker = createWorker();
          await worker.load();
          await worker.loadLanguage("rus");
          await worker.initialize("rus");

          // await clearThis();
          console.log("reconizing: ", data.name);
          while (true) {
            try {
              const result = await worker.recognize(part, "rus");
              console.log("reconizing complete: ", data.name);
              await worker.terminate();
              return result;
            } catch (err) {
              console.log("oof dats died: ", data.name);
            }
          }




          // await clearThis();
        });

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
          const scan = await parsingQueue.add(async () => {
            const worker = createWorker();
            await worker.load();
            await worker.loadLanguage("rus");
            await worker.initialize("rus");
  
            // await clearThis();
            console.log("reconizing: ", data.name);
            while (true) {
              try {
                const result = await worker.recognize(part, "rus");
                console.log("reconizing complete: ", data.name);
                await worker.terminate();
                return result;
              } catch (err) {
                console.log("oof dats died: ", data.name);
              }
            }
  


            // await clearThis();
          });
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
  }));

  console.log("reconizing complete.");

  // await clearThis();

  saveImage(buffer, docName, "_image");

  const flattenData = result.reduce((acc, data) => ({ ...acc, [data.key]: data.value }), {});

  return flattenData;
};

const init = () => {
  const app = express();

  app.use(bodyParser.json());

  app.use((req, res, next) => {
    console.log("Request: ", req.url);
    next();
  });

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
      res.status(500).send({ message: "Некорректное изображение", error: err });
    }
  });

  app.listen(5050, () => {
    console.log("server listening");
  });
};

init();
