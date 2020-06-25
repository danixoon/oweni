<?php
require_once realpath(__DIR__ . "/../config.php");
require_once realpath(__DIR__ . "/../template/main.php");

if (isset($_SESSION["user"])) {


  if (isset($_SESSION["user"])) {
    switch ($ACTION) {
      case "logout": {  
        Logout();
      }
    }
  }
  echo "Вы успешно вошли в аккаунт.";
} else
  render_auth();
