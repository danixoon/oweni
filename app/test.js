const fs = require("fs");
const path = require("path");
const { parseDocument, schemas, SCHEMA_TYPE } = require("./parser");

const fileName = process.argv[2] || SCHEMA_TYPE.RECRUIT_CASE;

const image = fs.readFileSync(path.resolve(__dirname, `./test/${fileName}.png`));

parseDocument(fileName, image).then(console.log);
