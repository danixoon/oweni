<!DOCTYPE html>
<html lang="ru">
<?php
// Подключение файла без его вывода на страницу
require_once(realpath(dirname(__FILE__) . "/../config/config.php"));
include("./table.php");

function isNotEmpty($input)
{
    $strTemp = $input;
    $strTemp = trim($strTemp);

    if (strlen($strTemp) > 0) {
        return true;
    }

    return false;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["action"] == 1) {
        //получение данный о том, какая таблица открыта в данный момент
        //и удаление данного значения POST для перерендера


        $table = $_POST["table"];
        unset($_POST["table"]);
        unset($_POST["action"]);
        unset($_POST["id"]);

        //Взятие всех ключей из POST сигнальных переменных
        // (там содержатся данные с отправленных инпутов из формы редактора)
        $keys = array_keys($_POST);
        //Создание запроса, пропаршифая массив в строку, а строку в запрос
        $keyQuery = implode(", ", $keys);
        $query = "INSERT INTO {$table} ({$keyQuery}) VALUES ";
        $values = array();

        foreach ($keys as $key) {
            if (is_numeric($_POST[$key])) {
                array_push($values, "{$_POST[$key]}");
            } else {
                array_push($values, "\"{$_POST[$key]}\"");
            }
        }

        // Дописание запроса путём склейки заполненных значений с ключами(именами строк)
        $query .= '(' . implode(", ", $values) . ");";
        // Отправка закроса на SLQ
        $result = $mysqli->query($query);
         print_r($query);
    }
    if ($_POST["action"] == 2) {
        $table = $_POST["table"];
        if (!isset($_POST['rownumber'])) {
            return;
        } else {
            $query = "DELETE FROM {$table} WHERE id={$_REQUEST['rownumber']}";
            // print_r("Запрос на удаление отправлен");
            $mysqli->query($query);
        }
        // print_r($query);
    }
    if ($_POST["action"] == 3) {
        $table = $_POST["table"];
        unset($_POST["id"]);

        echo "<table border='1px'>";
        echo "<tr>";
        echo "<td>";
        echo "Название книги";
        echo "</td>";

        echo "<td>";
        echo "Себестоимость, руб";
        echo "</td>";

        echo "<td>";
        echo "Цена продажи, руб";
        echo "</td>";

        echo "<td>";
        echo "Количество экземпляров";
        echo "</td>";

        echo "<td>";
        echo "Прибыль от продажи книги, руб";
        echo "</td>";
        echo "</tr>";

        $keyss = array("Name", "SelfCost", "SellCost");
        $resultt = $mysqli->query("SELECT * FROM books");
        // Рендеринг строк
        $summ;
        while ($rows = $resultt->fetch_assoc()) {
            echo "<tr>";
            foreach ($keyss as $value) {
                echo "<td>";
                echo $rows[$value];
                echo "</td>";
            }
            $resulttt = $mysqli->query("SELECT BooksCount FROM tasks 
                                                INNER JOIN books ON tasks.BooksId = books.id ");
            $rowss = $resulttt->fetch_assoc();
            echo "<td>";
            echo $rowss["BooksCount"];
            echo "</td>";

            echo "<td>";
            echo ($rows["SellCost"] - $rows["SelfCost"]) * $rowss["BooksCount"];
            echo "</td>";
            $summ = $summ + (int) ($rows["SellCost"] - $rows["SelfCost"]) * $rowss["BooksCount"];

            echo "</tr>";
        }
        echo "</table>";
        $resultttt = $mysqli->query("SELECT Name FROM customers 
                                    INNER JOIN tasks ON customers.id = tasks.CustormersId");
        $rowsss = $resultttt->fetch_assoc();
        echo ("<p>Заказчик:  " . $rowsss["Name"] . "</p>");
        echo ("<p>Итого:   " . $summ . "</p>");
    }
    if ($_POST["action"] == 4) {
        //получение данный о том, какая таблица открыта в данный момент
        //и удаление данного значения POST для перерендера


        $table = $_POST["table"];
        $rownumber = $_REQUEST['rownumber'];
        unset($_POST["table"]);
        unset($_POST["id"]);
        unset($_POST["rownumber"]);
        unset($_POST["action"]);

        //Взятие всех ключей из POST сигнальных переменных
        // (там содержатся данные с отправленных инпутов из формы редактора)
        $keys = array_keys($_POST);
        //Создание запроса, пропаршифая массив в строку, а строку в запрос
        $keyQuery = implode(", ", $keys);
        $query = "UPDATE {$table} SET ";
        $values = array();
        foreach ($keys as $key) {
            // print_r($_POST[$key]);
            if ($_POST[$key] == "") $_POST[$key] = null;
            array_push($values, "{$key} = \" {$_POST[$key]} \"");
        }
        if ($_REQUEST['Housing'] == null) $_REQUEST['Housing'] = '  ';
        $_REQUEST['City'] = ' \" {$_REQUEST[\'City\']} \" ';
        $_REQUEST['Street'] = ' \" {$_REQUEST[\'Street\']}\" ';

        // Дописание запроса путём склейки заполненных значений с ключами(именами строк)
        $query .= implode(" , ", $values) . " WHERE id={$rownumber}";
        // Отправка закроса на SLQ
        $result = $mysqli->query($query);
        // print_r($query);
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="author" content="Author">
    <title><?php
            echo $table ?>
    </title>
    <script>
        //Функция создаёт новый POST запрос,
        // отправляя значение с текущей открытой таблицей
        function redirect(page) {
            window.location.href = '/page/main.php?table=' + page;
        }
    </script>
</head>

<body>
    <!-- Кнопки для перемещения между таблицами
         При нажатии вызывают метод onclick, описанный выше с определённым параметром
     -->
    <div>
        <button onclick="redirect('adress')">Адресная таблица</button>
        <button onclick="redirect('authors')">Авторская таблица</button>
        <button onclick="redirect('books')">Книжная таблица</button>
        <button onclick="redirect('contastface')">Таблица Контрактного Лица</button>
        <button onclick="redirect('contracts')">Контрактная таблица</button>
        <button onclick="redirect('customers')">Клиентская таблица</button>
        <button onclick="redirect('tasks')">Заказная таблица</button>
    </div>

    <?php
    //Включение формы редактора в страницу
    require_once("editor.php");
    //если открылась какая-то таблица - прорисовать её
    if ($_REQUEST["table"]  == "null; ?>") return;
    if (isset($_REQUEST["table"]))
        render_table($_REQUEST["table"]);




    ?>
</body>

</html>