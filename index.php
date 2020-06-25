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
    array_push($menu_items, array(
      "name" => $value["name"],
      "action" => $ACTION,
      "link" => "/{$key}",
      "selected" =>  $SECTION === $key,
      "group" => $value["group"]
    ));
  }
  render_menu($menu_items);

  ?>
  <!--   ------------------------------------------------------------------>
  <div class="content">
    <div class="header shadow">
      <div class="img-header"></div>
      <?php if ($_SESSION["user"]) echo "<button onclick=\"fetch('/api/logout.php').then(() => window.location.reload())\" class='logout__button'> Выйти </button>" ?>
      <h1><?php echo $PAGE_SCHEMA[$PAGE]["name"]  ?></h1>
    </div>
    <main class="page">
      <?php
      include_once realpath(__DIR__ . "/page/{$SECTION}.php");
      ?>
    </main>
  </div>
</body>

</html>