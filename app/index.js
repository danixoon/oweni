const { createWorker } = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");
const express = require("express");
const bodyParser = require("body-parser");
const multer = require("multer");
const { parseDocument, schemas } = require("./parser");

const upload = multer({ storage: multer.memoryStorage() });

// /process.env.TESSDATA_PREFIX = path.resolve(__dirname);

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
