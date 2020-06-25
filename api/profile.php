<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") return send_error(404, "Неверный метод запроса.");

$post_data = ["education" => [], "relative" => [], "profile" => []];
foreach ($_POST as $key => $value) {
  $exploded = explode("-", $key);
  $table = $exploded[0];
  $field = $exploded[1];
  $id = $exploded[2];

  if (!isset($post_data[$table][$id])) {
    $post_data[$table][$id] = array();
  }

  $post_data[$table][$id][$field] = $value;
}

$queries = [];

foreach ($post_data as $table => $rows) {
  foreach ($rows as $data) {
    $id = $data["id"];
    $data_without_ids = array_filter($data, function ($value, $key) {
      return $key !== "id" && $key !== "profile_id";
    }, ARRAY_FILTER_USE_BOTH);

    $values = array_map(function ($value, $key) {

      return "`$key`='$value'";
    }, $data_without_ids, array_keys($data_without_ids));

    $query_values = implode(", ", $values);

    array_push($queries, "UPDATE `$table` SET $query_values WHERE `id`='$id'");
  }
}

// $query = implode(";", $queries);
// $mysqli->multi_query($query);
// do {
//   if ($mysqli->errno)

foreach ($queries as $query) {
  if (!$mysqli->query($query))
    return send_error(500, "Произошла ошибка в запросе: " . $mysqli->error, ["query" => $query, "error" => $mysqli->error]);
}
// } while ($mysqli->next_result());
