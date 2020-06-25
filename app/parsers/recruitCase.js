const { createWorker } = require("tesseract.js");
const util = require("util");
const fs = require("fs");
const sharp = require("sharp");
const path = require("path");

const parser = async ({ docName, docImage, trainLoadersQueue, extractValue, saveImage, parsingQueue, DATA_TYPE, schemas }) => {
  const schema = schemas[docName];
  const imageCrop = sharp(docImage).resize({ width: 1240, height: 1754, fit: "contain" }).trim(33);

  const buffer = await imageCrop.toBuffer();
  await util.promisify(fs.rmdir)(path.resolve(__dirname, `./result/${docName}`), { recursive: true });

  console.log("creating image dir");

  const scanData = async (data, part) => {
    console.log("reconizing: ", data.name);

    const worker = createWorker();

    await trainLoadersQueue.add(async () => {
      await worker.load();
      await worker.loadLanguage("rus");
      await worker.initialize("rus");

      return worker;
    });

    const result = await worker.recognize(part, "rus");
    console.log("reconizing complete: ", data.name);
    await worker.terminate();
    await new Promise(
      (res) =>
        setTimeout(() => {
          res();
        }),
      150
    );

    return result;

    // await clearThis();
  };

  console.log("parsing schema");

  const result = await Promise.all(
    schema.map(async (data) => {
      console.log("parsing schema field");
      switch (data.type) {
        case DATA_TYPE.TEXT: {
          const [left, top, right, bottom] = data.bounds.map((c) => c * 2);
          const part = await imageCrop.extract({ left, top, width: right - left, height: bottom - top }).toBuffer();
          saveImage(part, docName, data.name);
          const scan = await parsingQueue.add(() => scanData(data, part));

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
            const scan = await parsingQueue.add(() => scanData(data, part));
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

          const filteredRows = rows.filter((row) => {
            const rowKeys = Object.keys(row);
            return columnNames.every((columnName) => rowKeys.includes(columnName));
          });

          return {
            key: data.name,
            value: filteredRows,
          };
        }
      }
    })
  );

  console.log("reconizing complete.");

  // await clearThis();

  saveImage(buffer, docName, "_image");

  const flattenData = result.reduce((acc, data) => ({ ...acc, [data.key]: data.value }), {});
  return flattenData;
};

module.exports = parser;
