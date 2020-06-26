<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

$PAGE = $_REQUEST["page"];
$PAGE_SCHEMA = array(
  "main" => array("name" => "Главная"),
  "task" => array(
    "name" => "Список
    ",
    "group" => array(
      array("name" => "Поиск", "action" => "search", "link" => "/task/search"),
      array("name" => "Обработака", "action" => "add", "link" => "/task/add")
    )
  )
);

$SECTION = explode('/', $PAGE)[0];
$ACTION = explode("/", $PAGE)[1];

$mysqli = new mysqli("localhost", "root", "root", "oweni");

function start_with($string, $predicate)
{
  return substr($string, 0, strlen($predicate)) === $predicate;
}


function send_post($url, $payload)
{
  if ($curl = curl_init()) {
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS,  $payload);
    $out = curl_exec($curl);
    curl_close($curl);
    $err = curl_error($curl);
    return $out;
  }
}

function qu($var)
{
  return "'$var'";
}


function send_error($code, $message, $data = [])
{
  http_response_code($code ?? 500);
  echo json_encode(["message" => $message, "data" => $data]);
}
