<?php
require_once realpath(__DIR__ . "/../config.php");

function render_task_search($items = array())
{
?>
  <form class="task-search__form">
    <label>
      ФИО
      <input value="<?php echo $_GET["name"] ?>" name="name">
    </label>
    <button type="submit"> Найти </button>
  </form>
  <table class="task-search__table">
    <?php
    foreach ($items as $item) {
      echo "<tr>";
      foreach ($item as $key => $value) {
        echo "<td>";
        echo $value;
        echo "</td>";
      }
      echo "</tr>";
    }
    ?>
  </table>
<?php
}

function render_task_add()
{
?>

  <form enctype="multipart/form-data" method="POST" action="/api/parse.php" class="task-add__form" onsubmit="return onFormSubmit(this)">
    <select name="name">
      <option selected value="recruitCase"> Анекта </option>
    </select>
    <input type="file" name="image">
    <button type="submit"> Обработать </button>
  </form>
  <?php
}


function  render_task_list()
{
  global $mysqli;
  $res = $mysqli->query("SELECT * FROM `profile`");
  echo "<div style='display: flex; flex-flow: row wrap;'>";
  while ($row = $res->fetch_assoc()) {


  ?>
    <div style="width:15%; height: 200px; margin:20px; padding:0; flex-basis: 250px;" class="white shadow">
      <p style="text-align:left" class="text">
        <?php
        echo $row["id"];
        ?>
      </p>
      <p>
        <?php
        echo $row["name"];
        ?>
      </p>
      <p>
        <?php
        echo $row["living_address"];
        ?>
      </p>
      <p>
        <?php
        echo $row["private_phone"];
        ?>
      </p>
    </div>



<?php
  }
  echo "</div>";
}
