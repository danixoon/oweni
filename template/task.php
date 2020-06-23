<?php


function render_task_search($items = array())
{
?>
  <form class="task-search__form" onsubmit="return onFormSubmit(this)">
    <input name="ФИО">
    <input name="Категория">
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
