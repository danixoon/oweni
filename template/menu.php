<?php

function render_menu(array $items)
{
?>
  <div class="menu">
    <?php
    foreach ($items as $item) {
      $link = $item["link"];
      $name = $item["name"];
      $selected_class = $item["selected"] ? "selected" : "";

      echo "<a href='{$link}'>";
      echo "<div class='button {$selected_class}'>";
      echo "<div class='mtext'>{$name}</div>";
      echo "</div>";
      echo "</a>";
    }
    ?>
  </div>
<?php
}

?>