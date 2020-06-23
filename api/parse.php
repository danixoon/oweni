<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") return http_response_code(404);
if (!$_SESSION["user"]) return http_response_code(403);

$name = $_REQUEST["name"];
$image = $_FILES["image"];

send_post("localhost:5050/api/document/parse?name=$name", array("image" => new CURLFile($image["tmp_name"], $image["type"], $image["name"])));

http_response_code(200);
