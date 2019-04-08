<?php
/**
 * Created by PhpStorm.
 * User: timur
 * Date: 22.12.2018
 * Time: 20:19
 */

require_once "./lib/safemysql.class.php";
require_once "./lib/kint.phar";

Kint\Renderer\RichRenderer::$folder = false;
$db_config =[
    'user' => '',
    'pass' => '',
    'db' => '',
    'charset' => 'utf8'
];


$db = new SafeMySQL($db_config);
if (empty($_POST)) {
    $beginDate = date("Y-m-d 00:00:00");
    $endDate = date("Y-m-d 23:59:59");
    $logs = $db->getAll("SELECT * FROM `logs` WHERE `date` >= ?s AND `date` <= ?s ORDER BY `date` DESC LIMIT 25", $beginDate, $endDate);
} else {
    $beginDate = date("Y-m-d 00:00:00", strtotime($_POST['beginDate']));
    $endDate = date("Y-m-d 23:59:59", strtotime($_POST['endDate']));
    $logs = $db->getAll("SELECT * FROM `logs` WHERE `date` >= ?s AND `date` <= ?s AND `module` =?s AND `level` =?s ORDER BY `date` DESC", $beginDate, $endDate, $_POST['module'], $_POST['level']);
}
$levels = $db->getCol("SELECT DISTINCT `level` FROM `logs`");
$modules = $db->getCol("SELECT DISTINCT `module` FROM `logs`");
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Логгер</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="card" style="width: 100%">
            <div class="card-header">
                Фильтр
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="beginDate">От:</label>
                                <input type="date" class="form-control" id="beginDate" name="beginDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="endDate">До:</label>
                                <input type="date" class="form-control" id="endDate" name="endDate">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="level">Уровень</label>
                            <select name="level" id="level" class="form-control">
                                <?php
                                foreach ($levels as $level) {
                                    echo "<option value='" . $level . "'>" . $level . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="module">Модуль</label>
                            <select name="module" id="module" class="form-control">
                                <?php
                                foreach ($modules as $module) {
                                    echo "<option value='" . $module . "'>" . $module . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Показать</button>
                </form>
            </div>
        </div>
    </div>
    <hr>
    <?php
    foreach ($logs as $log) {
        $bg_class = "";
        switch ($log['level']) {
            case "info":
                $bg_class = "bg-info";
                break;

            case "warning":
                $bg_class = "bg-warning";
                break;

            case "error":
                $bg_class = "bg-danger";
                break;
        }
        echo "<div class='row'>";
        echo '<div class="card" style="width: 100%; margin-bottom: 20pt">
                <div class="card-header ' . $bg_class . '">
                    <span class="float-right">' . date("d.m.Y H:i:s", strtotime($log['date'])) . '</span>' . $log['module'] . '</span>
                  </div>
                <div class="card-body">
                <p class="card-text">';
        $data = unserialize(base64_decode($log['text']));
        d($data);
        echo '</p></div></div></div>';
    }
    ?>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>