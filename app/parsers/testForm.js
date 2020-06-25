const { createWorker } = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");

const mapper = {
  truthy: [1, 4, 6, 8, 9, 11, 16, 17, 18, 22, 25, 31, 34, 36, 43],
  correct: [
    3,
    5,
    7,
    10,
    15,
    20,
    26,
    27,
    29,
    32,
    33,
    35,
    37,
    40,
    41,
    42,
    44,
    45,
    47,
    48,
    49,
    50,
    51,
    52,
    53,
    56,
    57,
    59,
    60,
    62,
    63,
    64,
    65,
    66,
    67,
    69,
    70,
    71,
    72,
    73,
    74,
    75,
    76,
    77,
    78,
    79,
    80,
    81,
    82,
    83,
    84,
  ],
  incorrect: [2, 12, 13, 14, 19, 21, 23, 24, 28, 30, 38, 39, 46, 54, 55, 58, 61, 68],
};

const parser = async ({ docName, docImage, trainLoadersQueue, from = 1, extractValue, saveImage, parsingQueue, DATA_TYPE, schemas }) => {
  // const to = 33;

  const width = 130;
  const maxWidth = 612;

  let image = sharp(docImage).trim().resize(maxWidth);
  let imageBuffer = await image.toBuffer();

  const metadata = await sharp(imageBuffer).metadata();

  saveImage(imageBuffer, docName, "_image");

  const part = await image
    .extract({ left: maxWidth - width, top: 0, width, height: metadata.height })
    .extend({
      bottom: 5,
      left: 5,
      top: 5,
      right: 5,
      background: {
        alpha: 1,
        r: 255,
        g: 255,
        b: 255,
      },
    })
    .toBuffer();

  saveImage(part, docName, `list`);
  const worker = createWorker();

  await trainLoadersQueue.add(async () => {
    await worker.load();
    await worker.loadLanguage("rus");
    await worker.initialize("rus");
  });

  const result = await worker.recognize(part, "rus");
  const answers = result.data.text.split("\n").filter((v) => v);

  let truthy = 0;
  let score = 0;

  answers.forEach((an, i) => {
    const ok = an === "ДА";

    const id = i + from;

    truthy += ok && mapper.truthy.includes(id) ? 1 : 0;
    score += mapper.correct.includes(id) && ok ? 1 : mapper.incorrect.includes(id) && !ok ? 1 : 0;
  });

  console.log("reconizing complete: ", answers);
  console.log("result: ", "truthy: ", truthy, "score: ", score);

  await worker.terminate();
};

module.exports = parser;
