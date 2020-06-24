<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") return http_response_code(404);
if (!$_SESSION["user"]) return http_response_code(403);

$name = $_REQUEST["name"];
$image = $_FILES["image"];

$response = send_post("localhost:5050/api/document/parse?name=$name", array("image" => new CURLFile($image["tmp_name"], $image["type"], $image["name"])));
if ($response) {
  $data = json_decode($response, true);
  $mapper = array(
    "name" => function () use ($data) {
      return qu($data["last_name"] . " " . $data["first_name"] . " " . $data["family_name"]);
    },
    "birthday" => qu($data["birthday"]),
    "citizenship" =>  qu($data["citizenship"]),
    "living_address" =>  qu($data["living_address"]),
    "off_address" =>   qu($data["official_address"]),
    "home_phone" =>  qu($data["home_phone"]),
    "private_phone" =>  qu($data["private_phone"]),
    "position" =>  qu($data["position"]),
    "education" => $data["education"],
    "languages" =>  qu($data["languages"]),
    "relative" =>  $data["relative"],
    "hobby" =>   qu($data["hobby"])
  );

  $mapped_data = array_map(function ($value) use ($data) {
    if (is_callable($value)) return $value();
    else return $value;
  }, $mapper);

  $profile_values = implode(", ", [
    "NULL",
    $mapped_data["position"],
    $mapped_data["living_address"],
    $mapped_data["off_address"],
    $mapped_data["name"],
    $mapped_data["birthday"],
    $mapped_data["private_phone"],
    $mapped_data["home_phone"],
    $mapped_data["languages"],
    $mapped_data["hobby"],
    $mapped_data["citizenship"]
  ]);
  $insert_profile = "INSERT INTO `profile` VALUES ($profile_values);";

  $profile_result = $mysqli->query($insert_profile);
  if (!$profile_result) {
    http_response_code(500);
    echo "Ошибка добавления профиля.";
    return;
  }

  $profile_id = qu($mysqli->query("SELECT LAST_INSERT_ID() AS `id`;")->fetch_assoc()["id"]);

  $education_values = implode(", ", array_map(function ($edu) use ($profile_id) {
    return "(" . implode(", ",  [
      "NULL",
      qu($edu["name"]),
      qu($edu["income"]),
      qu($edu["release"]),
      qu($edu["branch"]),
      $profile_id
    ]) . ")";
  }, $mapped_data["education"]));
  $insert_education = "INSERT INTO `education` VALUES $education_values";

  $relative_values = implode(", ", array_map(function ($rel) use ($profile_id) {
    return "(" . implode(", ",  [
      "NULL",
      qu("Родственник"),
      qu($rel["birthday"]),
      qu($rel["name"]),
      $profile_id
    ]) . ")";
  }, $mapped_data["relative"]));
  $insert_relative = "INSERT INTO `relative` VALUES $relative_values";

  $insert_relative_result = $mysqli->query("$insert_relative");
  $insert_education_result = $mysqli->query("$insert_education");

  if (!$insert_relative_result) {
    http_response_code(500);
    echo "Ошибка добавления родственных связей.";
    return;
  }

  if (!$insert_education_result) {
    http_response_code(500);
    echo "Ошибка добавления образования.";
    return;
  }

  http_response_code(200);
} else http_response_code(500);