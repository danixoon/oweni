<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") return http_response_code(404);
if (!$_SESSION["user"]) return http_response_code(403);


$name = $_REQUEST["name"];
// $images = $_FILES["image"];


function parse_date($date)
{
  $exploded = explode(".", $date);
  return $exploded[2] . "-" . $exploded[1] . "-" . $exploded[0];
}

$images = [];
$countfiles = count($_FILES['image']['name']);
for ($i = 0; $i < $countfiles; $i++)
  array_push($images, []);


// Looping all files
// for ($i = 0; $i < $countfiles; $i++) {
$test = $_FILES["image"]["name"][0];
foreach ($_FILES['image'] as $key => $value) {
  $i = 0;
  foreach ($value as $img_value) {
    $images[$i][$key] = $img_value;
    $i++;
  }
}


// Upload file
// move_uploaded_file($_FILES['file']['tmp_name'][$i], 'upload/' . $filename);
// }

switch ($name) {
  case "recruitCase": {
      $image = $images[0];

      $response = send_post("localhost:5050/api/document/parse?name=$name", array("image" => new CURLFile($image["tmp_name"], $image["type"], $image["name"])));
      if ($response) {
        $data = json_decode($response, true);
        $mapper = array(
          "name" => function () use ($data) {
            return qu($data["last_name"] . " " . $data["first_name"] . " " . $data["family_name"]);
          },
          "birthday" => qu(parse_date($data["birthday"])),
          "citizenship" =>  qu($data["citizenship"]),
          "living_address" =>  qu($data["living_address"]),
          "off_address" =>   qu($data["off_address"]),
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
          $mapped_data["citizenship"],
          "NULL",
          "NULL"
        ]);
        $insert_profile = "INSERT INTO `profile` VALUES ($profile_values);";

        $profile_result = $mysqli->query($insert_profile);
        if (!$profile_result) {
          send_error(500, "Ошибка добавления профиля", ["mysql" => $mysqli->error, "query" => $insert_profile]);
          return;
        }

        $profile_id = qu($mysqli->query("SELECT LAST_INSERT_ID() AS `id`;")->fetch_assoc()["id"]);

        $education_values = implode(", ", array_map(function ($edu) use ($profile_id) {
          return "(" . implode(", ",  [
            "NULL",
            qu($edu["name"]),
            qu(parse_date($edu["income"])),
            qu(parse_date($edu["release"])),
            qu($edu["branch"]),
            $profile_id
          ]) . ")";
        }, $mapped_data["education"]));
        $insert_education = "INSERT INTO `education` VALUES $education_values";

        $relative_values = implode(", ", array_map(function ($rel) use ($profile_id) {
          return "(" . implode(", ",  [
            "NULL",
            qu("Родственник"),
            qu($rel["work_place"]),
            qu(parse_date($rel["birthday"])),
            qu($rel["name"]),
            $profile_id
          ]) . ")";
        }, $mapped_data["relative"]));
        $insert_relative = "INSERT INTO `relative` VALUES $relative_values";

        $insert_relative_result = $mysqli->query("$insert_relative");
        $insert_education_result = $mysqli->query("$insert_education");


        if (!$insert_relative_result) {
          send_error(500, "Ошибка добавления родственных связей.", ["mysql" => $mysqli->error, "query" => $insert_relative]);
          return;
        }

        if (!$insert_education_result) {
          send_error(500, "Ошибка добавления образования.", ["mysql" => $mysqli->error, "query" => $insert_education]);
          return;
        }

        http_response_code(200);
      } else return send_error(500, "Ошибка отправки запроса обработки");
      break;
    }
  case "testForm": {
      $profile_id = $_REQUEST["profile_id"];
      $score = 0;
      $truthy = 0;

      foreach ($images as $image) {
        $response = send_post("localhost:5050/api/document/parse?name=$name", array("image" => new CURLFile($image["tmp_name"], $image["type"], $image["name"])));
        if ($response) {
          $data = json_decode($response, true);
          $score += $data["score"];
          $truthy += $data["truthy"];
        } else return send_error(500, "Ошибка отправки запроса обработки");
      }


      $query = "UPDATE `profile` SET `score`='$score', `truethy`='$truthy' WHERE `id`='$profile_id'";
      if (!$mysqli->query($query)) {
        return send_error(500, "Ошибка обновления данных профиля");
      }

      http_response_code(200);

      break;
    }
}
