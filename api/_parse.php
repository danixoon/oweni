<?php

require_once realpath(__DIR__ . "/../config.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") return http_response_code(404);
if (!$_SESSION["user"]) return http_response_code(403);

$name = $_REQUEST["name"];
$image = $_FILES["image"];

function parse_date($date)
{
    $exploded = explode(".", $date);
    return $exploded[2] . "-" . $exploded[1] . "-" . $exploded[0];
}

$response = send_post("localhost:5050/api/document/parse?name=$name", array("image" => new CURLFile($image["tmp_name"], $image["type"], $image["name"])));
if ($response) {
    $data = json_decode($response, true);
    print_r($response);
    $mapper = array(
        "name" => function () use ($data) {
            return qu($data["last_name"] . " " . $data["first_name"] . " " . $data["family_name"]);
        },
        "truethy" =>   qu($data["truethy"]),
        "score" =>   qu($data["score"])
    );

    $mapped_data = array_map(function ($value) use ($data) {
        if (is_callable($value)) return $value();
        else return $value;
    }, $mapper);

    $profile_values = implode(", ", [
        "NULL",
        "NULL",
        "NULL",
        "NULL",
        $mapped_data["name"],
        "NULL",
        "NULL",
        "NULL",
        "NULL",
        "NULL",
        "NULL",
        $mapped_data["truethy"],
        $mapped_data["score"]
    ]);
    $insert_profile = "INSERT INTO `profile` VALUES ($profile_values);";

    $profile_result = $mysqli->query($insert_profile);
    if (!$profile_result) {
        send_error(500, "Ошибка добавления профиля", ["mysql" => $mysqli->error, "query" => $insert_profile]);
        return;
    }
    http_response_code(200);
    header("Location: /task");
} else http_response_code(500);
