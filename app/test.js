const fs = require("fs");
const path = require("path");
const { parseDocument, schemas, SCHEMA_TYPE } = require("./parser");

const image = fs.readFileSync(path.resolve(__dirname, "./test/recruitCase.png"));

parseDocument(SCHEMA_TYPE.RECRUIT_CASE, image).then(console.log);
