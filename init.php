<?php
$con = mysqli_connect("127.0.0.1", "root", "root", "doingsdone", 3308);

if (!$con) {
  die("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");