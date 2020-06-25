<?php
require_once(realpath(dirname(__FILE__) . "/../config.php"));


    $i = 1;
    $render_card = function ($j) use ($mysqli, $res_) {
        $res_ = $mysqli->query("SELECT * FROM `profile` WHERE id=${j}");
        $ids = mysqli_fetch_assoc($res_);

?>
        <article class="card shadow">
            <h2><?php echo ($ids['name']); ?> / <?php echo ($j); ?></h2>
            <h2 class="mtext">true</h2>
        </article>

        
<?php
    };

    while (true) {
        if ($i == $result->num_rows + 1) {
            break;
        }
        $render_card($i);
        $i++;
    }
?>

