<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") return http_response_code(404);

$username = $_GET["username"];
$password = $_GET["password"];

// $mysqli->query("")

if ($username === "test" && $password === "1488") {
  http_response_code(200);
  // $_SESSION["auth"] = array("")
}
