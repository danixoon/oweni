<?php
require_once realpath(__DIR__ . "/../config.php");
require_once realpath(__DIR__ . "/../template/auth.php");

if (isset($_SESSION["user"])) {
  echo "Вы успешно вошли в аккаунт.";
} else
  render_auth();
