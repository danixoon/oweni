<?php
session_start();

$PAGE = $_REQUEST["page"];
$PAGE_SCHEMA = array(
  "main" => array("name" => "Главная"),
  "task" => array(
    "name" => "Личные дела",
    "group" => array(
      array("name" => "Поиск", "action" => "search", "link" => "/task/search")
    )
  )
);

$SECTION = explode('/', $PAGE)[0];
$ACTION = explode("/", $PAGE)[1];

$mysqli = new mysqli("localhost", "root", "", "oweni");

function start_with($string, $predicate)
{
  return substr($string, 0, strlen($predicate)) === $predicate;
}
