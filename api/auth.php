<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") return http_response_code(404);

$username = $_REQUEST["username"];
$password = $_REQUEST["password"];

// $mysqli->query("")

$result = $mysqli->query("SELECT * FROM `account` WHERE `login`='{$username}' AND `password`='{$password}' LIMIT 1");
if (!$result) {
  http_response_code(500);
  echo json_encode($mysqli->error);
  return;
}

$user = $result->fetch_assoc();
if (!$user) {
  return http_response_code(403);
 
}

$_SESSION["user"] = $user;
http_response_code(200);
