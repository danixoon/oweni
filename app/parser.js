const { createWorker } = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");
const express = require("express");
const bodyParser = require("body-parser");
const multer = require("multer");
const { default: Queue } = require("p-queue");

const recruitCaseParser = require("./parsers/recruitCase");
const testFormParser = require("./parsers/testForm");

const DATA_TYPE = {
  TEXT: "string",
  TABLE: "table",
};

const SCHEMA_TYPE = {
  RECRUIT_CASE: "recruitCase",
  TEST_FORM: "testForm",
};

const extractValue = (text) => text.replace(/\s+/, " ").replace("\n", "").trim();

const schemas = {
  [SCHEMA_TYPE.TEST_FORM]: [
    {
      name: "testCase",
      bounds: [1, 73 / 2, 1236 / 2, 164 / 2],
      type: DATA_TYPE.TEXT,
    },
  ],
  [SCHEMA_TYPE.RECRUIT_CASE]: [
    {
      name: "last_name",
      bounds: [71, 40, 620, 61],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "first_name",
      bounds: [71, 65, 620, 87],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "family_name",
      bounds: [71, 87, 620, 108],
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
      bounds: [256, 150, 620, 180],
      type: DATA_TYPE.TEXT,
    },
    {
      name: "off_address",
      bounds: [260, 187, 620, 215],
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
        income: [155, 395, 310, 480],
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
        name: [0, 596, 217, 755],
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

const parsingQueue = new Queue({ concurrency: 8 });
const trainLoadersQueue = new Queue({ concurrency: 1 });

const parseDocument = (docName, docImage, props) => {
  const config = { docImage, docName, DATA_TYPE, SCHEMA_TYPE, schemas, ...props, parseDocument, saveImage, extractValue, parsingQueue, trainLoadersQueue };

  switch (docName) {
    case SCHEMA_TYPE.RECRUIT_CASE:
      return recruitCaseParser(config);
    case SCHEMA_TYPE.TEST_FORM:
      return testFormParser(config);
    default:
      throw new Error("invalid docname");
  }
};

module.exports = { parseDocument, schemas, SCHEMA_TYPE, DATA_TYPE };
