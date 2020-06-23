<?php
require_once realpath(__DIR__ . "/../config.php");
require_once realpath(__DIR__ . "/../template/auth.php");

if (isset($_SESSION["user"])) {
  switch ($ACTION) {
    case "search": {
        
        break;
      }
    default:
      echo "Выберите действие для продолжения";
      break;
  }
} else
  echo "<script> alert('Необходимо аутентифицироваться.'); window.location.href='/main'; </script>";
