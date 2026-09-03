<?php
include_once("helpers.php");
include_once("functions.php");
include_once("data.php");

$page_content = include_template("main.php", [
  "projects" => $projects,
  "tasks" => $tasks,
  "show_complete_tasks" => $show_complete_tasks,
]);

$layout_content = include_template("layout.php", [
  "content" => $page_content,
  "title" => "Главная",
  "user_name" => $user_name,
]);

print($layout_content);