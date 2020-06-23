<?php

function render_menu(array $items)
{
?>
  <div class="menu">
    <?php
    foreach ($items as $item) {
      $link = $item["link"];
      $name = $item["name"];
      $group = $item["group"];
      $active_action = $item["action"];
      $selected_class = $item["selected"] ? "selected" : "";

      echo "<a href='{$link}'>";
      echo "<div class='button {$selected_class}'>";
      echo "<div class='mtext'>{$name}</div>";
      echo "<div class='menu-group'>";

      echo "</div>";
      echo "</div>";
      echo "</a>";
      if ($group) {
        echo "<div class='menu-group'>";
        foreach ($group as $action) {
          $name = $action["name"];
          $link = $action["link"];
          $action_name = $action["action"];

          $selected_class = $action_name === $active_action ? "selected" : "";

          echo "<a href='{$link}'>";
          echo "<div class='button {$selected_class}'>";
          echo "<div class='mtext'>{$name}</div>";
          echo "</div>";
          echo "</a>";
        }
        echo "</div>";
      }
    }
    ?>
  </div>
<?php
}

?>