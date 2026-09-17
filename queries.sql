-- Добавление пользователей
INSERT INTO users (name, email, password)
VALUES
    ('Рамиль', 'ramil@example.com', 'password_hash_1'),
    ('Анна', 'anna@example.com', 'password_hash_2');


-- Добавление проектов
INSERT INTO projects (name, user_id)
VALUES
    ('Входящие', 1),
    ('Учеба', 1),
    ('Работа', 1),
    ('Домашние дела', 1),
    ('Авто', 1);


-- Добавление задач
INSERT INTO tasks
(name, deadline, status, user_id, project_id)
VALUES
    ('Собеседование в IT-компании', '2019-12-01', 0, 1, 3),
    ('Выполнить тестовое задание', '2026-09-04', 0, 1, 3),
    ('Сделать задание первого раздела', '2019-12-21', 1, 1, 2),
    ('Встреча с другом', '2019-12-22', 0, 1, 1),
    ('Купить корм для кота', NULL, 0, 1, 4),
    ('Заказать пиццу', NULL, 0, 1, 4);


-- Получить список всех проектов для одного пользователя
SELECT name
FROM projects
WHERE user_id = 1;


-- Получить список всех задач для одного проекта
SELECT name
FROM tasks
WHERE project_id = 3;


-- Пометить задачу как выполненную
UPDATE tasks
SET status = 1
WHERE id = 2;


-- Обновить название задачи
UPDATE tasks
SET name = 'Выполнить тестовое задание для компании'
WHERE id = 2;