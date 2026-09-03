<?php
$projects = [
  'Входящие',
  'Учеба',
  'Работа',
  'Домашние дела',
  'Авто'
];

$tasks = [
  [
    'title' => 'Собеседование в IT-компании',
    'date' => '01.12.2019',
    'project' => 'Работа',
    'completed' => false
  ],
  [
    'title' => 'Выполнить тестовое задание',
    'date' => '25.12.2019',
    'project' => 'Работа',
    'completed' => false
  ],
  [
    'title' => 'Сделать задание первого раздела',
    'date' => '21.12.2019',
    'project' => 'Учеба',
    'completed' => true
  ],
  [
    'title' => 'Встреча с другом',
    'date' => '22.12.2019',
    'project' => 'Входящие',
    'completed' => false
  ],
  [
    'title' => 'Купить корм для кота',
    'date' => null,
    'project' => 'Домашние дела',
    'completed' => false
  ],
  [
    'title' => 'Заказать пиццу',
    'date' => null,
    'project' => 'Домашние дела',
    'completed' => false
  ]
];

$show_complete_tasks = rand(0, 1);

$user_name = "Рамиль";