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


function render_task_list()
{
  global $mysqli;
  $res = $mysqli->query("SELECT * FROM `profile`");
  echo "<div style='display: flex; flex-flow: row wrap;'>";
  while ($row = $res->fetch_assoc()) {


  ?>
    <script>
      function toggleCardEditModal() {
        $("#card-edit__modal").toggleClass("visible");
      }
    </script>
    <div id="card-edit__modal" class="modal">

      <div onclick="toggleCardEditModal()" class="modal__background"></div>
      <div class="modal__container">
        <div class="modal__content" style="  text-align: left;padding: 2%;">
          <label><span>ФИО</span><input type="text" value="<?php echo $row["name"]; ?>"></label>
          <label><span>Дата рождения</span><input type="text" value="<?php echo $row["birthday"]; ?>"></label>
          <label><span>Гражданство</span><input type="text" value="<?php echo $row["citizenship"]; ?>"></label>
          <label><span>Адрес проживания </span> <input type="text" value="<?php echo $row["living_address"]; ?>"></label>
          <label><span>Адрес прописки </span> <input type="text" value="<?php echo $row["off_address"]; ?>"></label>
          <label><span>Домашний телефон </span> <input type="text" value="<?php echo $row["home_phone"]; ?>"></label>
          <label><span>Мобильный телефон</span> <input type="text" value="<?php echo $row["private_phone"]; ?>"></label>
          <label><span>Семейное положение</span> <input type="text" value="<?php echo $row["position"]; ?>"></label>
          <label><span>Образование</span></label>
          <br>
          <label>
            <span style="flex:2; max-width:197px;">Название</span>
            <span style="flex:1; max-width:100px;">Поступление</span>
            <span style="flex:1; max-width:100px;">Окончание</span>
            <span style="flex:3;">Специальность</span></label>
          <?php
          $profile_id = $row['id'];
          $query = "SELECT * FROM education WHERE profile_id='$profile_id'";
          $edu_res = $mysqli->query($query);
          if ($edu_res) {
            while ($edu_row = $edu_res->fetch_assoc()) {
              $name = $edu_row["name"];
              $income = $edu_row["income"];
              $release = $edu_row["release"];
              $branch = $edu_row["branch"];
              echo "<label style='margin:-2px;'>";
              echo "<input style='flex:4; max-width:200px;' type='text' value='$name'>";
              echo "<input style='flex:4; max-width:100px;' type='text' value='$income'>";
              echo "<input style='flex:4; max-width:100px;' type='text' value='$release'>";
              echo "<input style='flex:4; min-width:200px;' type='text' value='$branch'>";
              echo "</label>";
            }
          } else {
            echo $mysqli->error;
          }
          ?>
          <br>
          <label><span>Знание языков</span> <input type="text" value="<?php echo $row["languages"]; ?>"></label>

          <label><span>Родственники</span></label>
          <label>
            <span style="flex:2;">Роль</span>
            <span style="flex:3;">Место работы</span>
            <span style="flex:2;">Дата рождения</span>
            <span style="flex:2;">ФИО</span></label>

          <?php
          $profile_id = $row['id'];
          $query = "SELECT * FROM relative WHERE profile_id='$profile_id'";
          $rel_res = $mysqli->query($query);
          if ($edu_res) {
            while ($rel_row = $rel_res->fetch_assoc()) {
              $role = $rel_row["role"];
              $work_place = $rel_row["work_place"];
              $birthday = $rel_row["birthday"];
              $name = $rel_row["name"];
              echo "<label style='margin:-2px;'>";
              echo "<input style='flex:2;' type='text' value='$role'>";
              echo "<input style='flex:3;' type='text' value='$work_place'>";
              echo "<input style='flex:1;' type='text' value='$birthday'>";
              echo "<input style='flex:3;' type='text' value='$name'>";
              echo "</label>";
            }
          } else {
            echo $mysqli->error;
          }

          ?>
          <br>
          <label><span>Увлечения</span> <input type="text" value="<?php echo $row["hobby"]; ?>"></label>
          <br>
          <br>
          <input style="float: bot;" type="button" value="Сохранить">
        </div>
      </div>
    </div>


    <script>
      function toggleCardRemoveModal() {
        $("#card-remove__modal").toggleClass("visible");
      }
    </script>

    <div id="card-remove__modal" class="modal">
      <div onclick="toggleCardRemoveModal()" class="modal__background"></div>
      <div class="modal__container">
        <div class="modal__content" style="  text-align: left;padding: 2%;">
          <form action="/api/remove.php" onsubmit="return onFormSubmit(this, '/task')">
            <label>Вы уверены?</label>
            <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
            <input type="submit" value="Удалить">
          </form>

        </div>
      </div>
    </div>
    <div style="width:15%; height: 200px; margin:20px; padding:0; flex-basis: 250px;" class="white shadow">
      <form action="*" method="POST">
        <select onchange="if(this.value === 'edit') { toggleCardEditModal()} else toggleCardRemoveModal();" style="width:20px; float:right">
          <option disabled selected value=""></option>
          <option value="edit">Изменить</option>
          <option value="remove">Удалить</option>
        </select>
      </form>
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
      <hr style="size:2px;">
      <p style="margin-top:25%;">
        <?php
        echo $row["home_phone"];
        ?>
      </p>
      <hr>
      <p>
        <?php
        echo $row["living_address"];
        ?>
      </p>
    </div>


