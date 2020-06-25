<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "GET") return http_response_code(404);


if( isset($_SESSION["user"])){
    session_destroy();
    $_SESSION["user"] = null;
}


http_response_code(200);

