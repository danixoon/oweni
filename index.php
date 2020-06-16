<?php
require_once realpath(__DIR__ . "/config.php");
require_once realpath(__DIR__ . "/template/header.php");
require_once realpath(__DIR__ . "/template/menu.php");

render_header("Главная");

?>

<body>
  <?php
  $menu_items = array();
  foreach ($PAGE_SCHEMA as $key => $value) {
    array_push($menu_items, array("name" => $value["name"], "link" => "/index.php?page={$key}", "selected" => $PAGE === $key));
  }
  render_menu($menu_items);
  ?>
  <!--   ------------------------------------------------------------------>
  <div class="content">
    <div class="header shadow">
      <div class="img-header"></div>
      <h1><?php echo $PAGE_SCHEMA[$PAGE]["name"] ?></h1>
    </div>
    <div class="plus shadow">
      <div class="header-plus blue">
        <a href="Profile.html" class="button" style="color: white;">Войдите</a>
      </div>
      <div class="content-plus">
      </div>

    </div>
    <p class="por">или</p>

    <div class="plus shadow">
      <div class="header-plus red">
        <a href="Profile.html" class="button" style="color: white;">Зарегистрируйтесь</a>
      </div>
      <div class="content-plus">

      </div>
    </div>
  </div>
</body>

</html>