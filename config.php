<?php

$PAGE = $_REQUEST["page"];
$PAGE_SCHEMA = array(
  "main" => array("name" => "Главная"),
  "task" => array("name" => "Личные дела")
);

$mysqli = new mysqli("localhost", "root", "root", "oweni");