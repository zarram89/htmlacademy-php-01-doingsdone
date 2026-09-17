<?php

include_once("helpers.php");
include_once("functions.php");
include_once("init.php");
include_once("models.php");
include_once("data.php");

$user_id = 1;

$projects = get_projects($con, $user_id);
$tasks = get_tasks($con, $user_id);

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