<?php

/**
 * Получает список проектов текущего пользователя.
 */
function get_projects($con, $user_id)
{
  $sql = "SELECT id, name
            FROM projects
            WHERE user_id = $user_id";

  $result = mysqli_query($con, $sql);

  if (!$result) {
    die("Ошибка запроса: " . mysqli_error($con));
  }

  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получает список задач текущего пользователя.
 */
function get_tasks($con, $user_id)
{
  $sql = "SELECT
                tasks.id,
                tasks.name,
                tasks.deadline,
                tasks.status,
                tasks.file_path,
                projects.name AS project
            FROM tasks
            JOIN projects ON tasks.project_id = projects.id
            WHERE tasks.user_id = $user_id";

  $result = mysqli_query($con, $sql);

  if (!$result) {
    die("Ошибка запроса: " . mysqli_error($con));
  }

  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}