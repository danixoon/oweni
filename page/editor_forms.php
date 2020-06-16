<?php
require_once(realpath(dirname(__FILE__) . "/../config/config.php"));

if (!isset($_REQUEST["table"]) || !isset($_REQUEST["table"])) {
  return;
}


function render_editor_add($table)
{
  global $TABLE_HEADERS;
  $keys = array_keys($TABLE_HEADERS["{$table}Table"]);
  array_splice($keys, 0,1);
  foreach ($keys as $key => $value) {

    echo "<label> {$TABLE_HEADERS["{$table}Table"][$value]} <input name='{$value}' /></label>";
  }
  echo "<input type='hidden' name='table' value='{$_REQUEST["table"]}'>";
  echo "<input type='hidden' name='action' value='1'>";
  echo "<button type='submit'> Отправить </button>";
}

function render_editor_rem($table)
{
  echo "<label>Укажите номер удаляемой строки<input name='rownumber'/></label>";
  echo "<input type='hidden' name='table' value='{$_REQUEST["table"]}'>";
  echo "<input type='hidden' name='action' value='2'>";
  echo "<button type='submit'> Отправить </button>";
}

function render_editor_report()
{
  echo "<input type='hidden' name='table' value='{$_REQUEST["table"]}'>";
  echo "<input type='hidden' name='action' value='3'>";
  echo "<button type='submit'> Отправить </button>";
}

function render_editor_edit($table)
{
  echo "<label>Укажите номер изменяемой строки<input name='rownumber'/></label>";
  global $TABLE_HEADERS;
  $keys = array_keys($TABLE_HEADERS["{$table}Table"]);
  array_splice($keys, 0,1);
  foreach ($keys as $key => $value) {
    echo "<label> {$TABLE_HEADERS["{$table}Table"][$value]} <input name='{$value}' /></label>";
  }
  echo "<input type='hidden' name='table' value='{$_REQUEST["table"]}'>";
  echo "<input type='hidden' name='action' value='4'>";
  echo "<button type='submit'> Отправить </button>";
}

$action = $_REQUEST["action"];
$table = $_REQUEST["table"];

switch ($action) {
  case 1: {
      render_editor_add($_REQUEST["table"]);
      break;
    }
  case 2: {
      render_editor_rem($_REQUEST["table"]);
      break;
    }
  case 3: {
      render_editor_report();
      break;
    }
  case 4: {
      render_editor_edit($_REQUEST["table"]);
      break;
    }
}
