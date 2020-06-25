<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") return http_response_code(404);

session_destroy();

http_response_code(200);
