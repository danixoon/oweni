<?php
require_once realpath(__DIR__ . "/config.php");
require_once realpath(__DIR__ . "/template/header.php");
require_once realpath(__DIR__ . "/template/menu.php");
require_once realpath(__DIR__ . "/template/card.php");
require_once realpath(__DIR__ . "/template/enterance.php");

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
         <?php  
          if($PAGE_SCHEMA[$PAGE]["name"] == "Главная"){
            render_enterance();
          }
          if($PAGE_SCHEMA[$PAGE]["name"] == "Личные дела"){
            render_card();
          }
         ?>



  </div>
</body>

</html>