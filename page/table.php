<?php
require_once(realpath(dirname(__FILE__) . "/../config/config.php"));

function render_table($table)
{
  
  global $mysqli;
  global $TABLE_HEADERS;
  // $mysqli->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
  echo  "<table border='1px'>";

  //Запрос для SQL таблицы с выводом всех полей из переданной таблицы
  $result = $mysqli->query("SELECT * FROM {$table}");
  $keys = array_keys($TABLE_HEADERS["{$table}Table"]);
  // Рендеринг заголовка
  echo "<tr>";
  for ($i = 0; $i < count($keys); $i++) {
    echo "<td>";
    echo $TABLE_HEADERS["{$table}Table"][$keys[$i]];
    echo "</td>";
  }
  echo "</tr>";

  // Рендеринг строк
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($keys as $value) {
      echo "<td>";
      echo $row[$value];
      echo "</td>";
    }
    echo "</tr>";
  }

  echo "</table>";
}
