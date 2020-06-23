<?php
require_once realpath(__DIR__ . "/../template/auth.php");

if (isset($_SESSION["auth"])) {
} else
  render_auth();
