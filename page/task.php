<?php
require_once realpath(__DIR__ . "/../config.php");
require_once realpath(__DIR__ . "/../template/task.php");

if (isset($_SESSION["user"])) {
  switch ($ACTION) {
    case "search": {


        $conditions = [];
        foreach ($_GET as $key => $value) {
          if (!empty($value) && $key !== "page") array_push($conditions, "`$key` LIKE '$value%'");
        }

        $condition_query = count($conditions) > 0 ? " WHERE " . implode(", ", $conditions) : "";

        $items = array(array("Ид.", "Семейное положение", "Адрес проживания", "Адрес прописки", "ФИО", "Дата рождения", "Моб. телефон", "Дом. телефон", "Знание языков", "Хобби", "Гражданство"));
        $result = $mysqli->query("SELECT * FROM `profile` $condition_query");

        if ($result)
          while ($item = $result->fetch_assoc()) array_push($items, $item);

        render_task_search($items);
        break;
      }
    case "add": {
        render_task_add();
        break;
      }
    default:
      render_task_list();
      break;
  }
} else
  echo "<script> alert('Необходимо аутентифицироваться.'); window.location.href='/main'; </script>";
