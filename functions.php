<?php
function countTasksByProject($tasks, $project) {
  $count = 0;

  foreach ($tasks as $task) {
    if ($task['project'] === $project) {
      $count++;
    }
  }

  return $count;
}