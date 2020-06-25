<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") return http_response_code(404);

$id = $_REQUEST["id"];

// $mysqli->query("")

$result1 = $mysqli->query("DELETE FROM profile WHERE id = $id;");
$result2 = $mysqli->query("DELETE FROM relative WHERE profile_id = $id;");
$result3 = $mysqli->query("DELETE FROM education WHERE profile_id = $id;");

if (!$result1) {
  http_response_code(500);
  echo json_encode($mysqli->error);
  return;
}

if (!$result2) {
    http_response_code(500);
    echo json_encode($mysqli->error);
    return;
  }

  if (!$result3) {
    http_response_code(500);
    echo json_encode($mysqli->error);
    return;
  }


http_response_code(200);
header("Location: /task");