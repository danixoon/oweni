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

  <form enctype="multipart/form-data" method="POST" action="/api/parse.php" class="task-add__form" onsubmit="return onFormSubmit(this, 'Location: /task')">
    <select name="name">
      <option value="recruitCase"> Анекта </option>
      <option selected value="testForm"> Тест НПУ </option>
    </select>
    <input placeholder="ид. профиля" type="number" name="profile_id">
    <input type="file" multiple="multiple" name="image[]">
    <button type="submit"> Об5работать </button>
  </form>
  <?php
}

function render_data($prefix, $data, $labels)
{
  $id = $data["id"];
  foreach ($data as $key => $value) {
    $label = $labels[$key];
    $disabled = (substr($key, strlen($key) - 3, 3) === "_id" || $key === "id") ? "disabled" : "";
    echo "<label><span>$label</span><input $disabled name='$prefix-$key-$id' type='text' value='$value'></label>";
  }
}

function render_task_list()
{

  function render_modal($profile_data, $profile_id)
  {
    global $mysqli;



    $education_fetch = $mysqli->query("SELECT * FROM `education` WHERE profile_id='$profile_id'");
    $relative_fetch = $mysqli->query("SELECT * FROM `relative` WHERE profile_id='$profile_id'");

    $education_data = $education_fetch ? $education_fetch->fetch_all(MYSQLI_ASSOC) : [];
    $relative_data = $relative_fetch ? $relative_fetch->fetch_all(MYSQLI_ASSOC) : [];

  ?>
    <div id="card-edit__modal-<?php echo $profile_id; ?>" class="modal">
      <div onclick="toggleCardEditModal(<?php echo $profile_id; ?>)" class="modal__background"></div>
      <div class="modal__container">
        <form method="POST" action="/api/profile.php" onsubmit="return onFormSubmit(this)" class="modal__content" style="text-align: left; padding: 2%;">
          <hr>
          <p> Профиль </p>
          <?php render_data("profile", $profile_data, [
            "id" => "Ид.",
            "name" => "ФИО",
            "birthday" => "Дата рождения",
            "citizenship" => "Гражданство",
            "living_address" => "Адрес проживания",
            "off_address" => "Адрес прописки",
            "home_phone" => "Домашний тел.",
            "private_phone" => "Личный тел.",
            "position" => "Семейное положение",
            "languages" => "Знание языков",
            "hobby" => "Хобби",
            "truethy" => "Баллы истинности",
            "score" => "Баллы НПУ"
          ]) ?>
          <hr>
          <p> Родственники </p>
          <div>
            <?php
            foreach ($relative_data as $rel_row) {
              echo "<div class='data-group'>";
              render_data("relative", $rel_row, [
                "id" => "Ид.",
                "role" => "Роль",
                "work_place" => "Место работы",
                "birthday" => "Дата рождения",
                "name" => "ФИО",
                "profile_id" => "Ид. профиля"
              ]);
              echo "</div>";
            }
            ?>
          </div>
          <p> Образование </p>
          <hr>
          <div>
            <?php
            foreach ($education_data as $edu_data) {
              echo "<div class='data-group'>";
              render_data("education", $edu_data, [
                "id" => "Ид.",
                "branch" => "Специальность",
                "income" => "Год поступления",
                "release" => "Год окончания",
                "name" => "ФИО",
                "profile_id" => "Ид. профиля"
              ]);
              echo "</div>";
            }
            ?>
          </div>
          <button type='submit'> Сохранить </button>
        </form>
      </div>
    </div>


    <div id="card-remove__modal-<?php echo $profile_id; ?>" class="modal">
      <div onclick="toggleCardRemoveModal(<?php echo $profile_id; ?>)" class="modal__background"></div>
      <div class="modal__container">
        <div class="modal__content" style="  text-align: left;padding: 2%;">
          <form action="/api/remove.php" onsubmit="return onFormSubmit(this)">
            <label>Вы уверены?</label>
            <input type="hidden" name="id" value="<?php echo $profile_id; ?>">
            <input type="submit" value="Удалить">
          </form>

        </div>
      </div>
    </div>

    <div id="form-edit__modal-<?php echo $profile_id; ?>" class="modal">
      <div onclick="toggleFormModal(<?php echo $profile_id; ?>)" class="modal__background"></div>
      <div class="modal__container">
        <div class="modal__content" style="  text-align: left;padding: 2%;">
          <form action="/api/remove.php" onsubmit="return onFormSubmit(this)">
            <label>Истиность: <?php echo $profile_data["truethy"]; ?></label>
            <label>Индекс НПУ: <?php echo $profile_data["score"]; ?></label>
            <label>ФИО заполнившего: <?php echo $profile_data["name"]; ?></label>
            <br>

            <input type="hidden" name="id" value="<?php echo $profile_id; ?>">
          </form>

        </div>
      </div>
    </div>
  <?php
  }



  global $mysqli;
  $res = $mysqli->query("SELECT * FROM `profile`");
  echo "<div style='display: flex; flex-flow: row wrap;'>";
  while ($row = $res->fetch_assoc()) {
    $profile_id = $row['id'];
  ?>
    <div style="width:15%; min-height: 200px; margin:20px; padding:0; flex-basis: 250px;" class="white shadow">
      <?php render_modal($row, $profile_id); ?>

      <select onchange="switch(this.value) {
            case 'edit':
              toggleCardEditModal(<?php echo $profile_id; ?>);
              break;
            case 'delete':
              toggleCardRemoveModal(<?php echo $profile_id; ?>);
              break;
              case 'testResult':
                toggleFormModal(<?php echo $profile_id; ?>);
                break;
          }
          " style="width:20px; float:right">
        <option disabled selected value=""></option>
        <option value="testResult">Результаты Теста</option>
        <option value="edit">Изменить</option>
        <option value="delete">Удалить</option>
      </select>

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
        echo "Истинность: " . $row["truethy"];
        ?>
      </p>
      <p>
        <?php
        echo "Баллы НПУ: " . $row["score"];
        ?>
      </p>
      <p>
        <?php
        // $score = $row["score"];
        // $result = "";
        // if ($score >= 29)
        //   $result =  "Высокая вероятность нервно-психических срывов. Необходимо дополнительное медобследование психиатра, невропатолога.";
        // else if ($score >= 14)
        //   $result = "Нервно-психические срывы вероятны, особенно в экстремальных условиях. Необходимо учитывать этот факт при вынесении заключения о пригодности.";
        // else
        //   $result = "Нервно-психические срывы маловероятны. При наличии других положительных данных можно рекомендовать на специальности, требующие повышенной НПУ.";

        // echo "Результат теста: $result";
        ?>
      </p>
    </div>
<?php
  }
}
