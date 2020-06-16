const Tesseract = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");

process.env.TESSDATA_PREFIX = path.resolve(__dirname, "./tessdata");

const extractValue = (text) =>
  text
    .split("")
    .filter((char) => {
      const c = char.charCodeAt(0);
      const ok = c === " " || (c >= 1024 && c <= 1279) || (c >= 48 && c <= 57);
      return ok;
    })
    .join("");

const schema = [
  {
    name: "lastName",
    bounds: [70, 46, 618, 71],
  },
  {
    name: "firstName",
    bounds: [70, 72, 618, 92],
  },
  {
    name: "someData",
    bounds: [0, 398, 154, 486],
  },
];

const saveImage = async (buffer, name) => {
  await util.promisify(fs.writeFile)(path.resolve(__dirname, `./result/${name}.png`), buffer, "binary");
};

const init = async () => {
  const image = await util.promisify(fs.readFile)(path.resolve(__dirname, "./test/image.png"));
  const imageCrop = sharp(image)
    .resize({ width: 1240 / 2, height: 1754 / 2, fit: "contain" })
    .trim(33);

  const buffer = await imageCrop.toBuffer();
  saveImage(buffer, "_image");

  const result = await Promise.all(
    schema.map(async (data) => {
      const [left, top, right, bottom] = data.bounds;
      const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
      saveImage(part, data.name);
      const scan = await Tesseract.recognize(part, "rus");

      return {
        name: data.name,
        value: scan.data.text
          .split("\n")
          .map((v) => extractValue(v))
          .filter((value) => value),
      };
    })
  );

  console.log(result);
};

init();
