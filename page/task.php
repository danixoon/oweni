<?php
require_once realpath(__DIR__ . "/../config.php");
require_once realpath(__DIR__ . "/../template/task.php");

if (isset($_SESSION["user"])) {
  switch ($ACTION) {
    case "search": {
        $items = array(array("Ид.", "Семейное положение", "Образование", "Адрес", "ФИО", "Дата должения", "Моб. телефон", "Дом. телефон", "Знание языков", "Хобби", "Гражданство"));
        $result = $mysqli->query("SELECT * FROM `profile`");
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
      echo "Выберите действие для продолжения";
      break;
  }
} else
  echo "<script> alert('Необходимо аутентифицироваться.'); window.location.href='/main'; </script>";
