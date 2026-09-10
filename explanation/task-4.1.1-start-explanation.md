Да. Для `htmlacademy-php-01-doingsdone` я бы сделал **тот же подход, что мы уже настроили для YetiCave**, только отдельный контейнер и отдельный порт.

Из ТЗ здесь прямо следуют **три сущности**: пользователь, проект и задача. Проект принадлежит пользователю, а задача связана и с автором-пользователем, и с проектом.

Получается модель:

```text
users
  │
  ├──────────────< projects
  │                   │
  │                   │
  └──────────────< tasks
                      │
projects ─────────────┘
```

То есть:

```text
users 1 ─── N projects
users 1 ─── N tasks
projects 1 ─ N tasks
```

Причём из ТЗ есть ещё два важных ограничения: email пользователя не должен повторяться, а название проекта не должно дублироваться **у одного и того же пользователя**.

## 1. `compose.yaml`

В корне:

```text
D:\htmlacademy\htmlacademy-php-01-doingsdone\
```

создаём:

```text
compose.yaml
```

```yaml
services:
  db:
    image: mysql:8.0
    container_name: doingsdone-mysql
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: doingsdone
    ports:
      - "3308:3306"
    volumes:
      - doingsdone_mysql_data:/var/lib/mysql

volumes:
  doingsdone_mysql_data:
```

Почему `3308`:

```text
3306 → твой глобальный MySQL Windows
3307 → YetiCave
3308 → DoingsDone
```

Так проекты вообще не конфликтуют:

```text
Windows
│
├── localhost:3306 → глобальный MySQL
│
├── localhost:3307 → yeticave-mysql
│
└── localhost:3308 → doingsdone-mysql
```

В задании требуется, чтобы SQL был совместим с **MySQL 5.7+**.  Мы можем использовать локально MySQL 8.0, при этом писать схему без конструкций, которых нет в 5.7.

---

# 2. Теперь выводим `schema.sql` из ТЗ

## `users`

ТЗ говорит:

```text
Пользователь:
- дата регистрации
- email
- имя
- пароль
```



Значит:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  name VARCHAR(128) NOT NULL,
  password VARCHAR(255) NOT NULL
);
```

---

## `projects`

Проект:

> состоит только из названия и имеет связь с пользователем, который его создал.

Получаем:

```sql
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,

  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

Но есть ещё требование:

> название проекта не должно дублировать уже существующие названия проектов **для данного пользователя**.

Поэтому здесь очень хорошо подходит **составной UNIQUE**:

```sql
UNIQUE (user_id, name)
```

Это не означает, что `"Работа"` вообще может существовать только один раз.

Так можно:

```text
user 1 → Работа
user 2 → Работа
```

А так нельзя:

```text
user 1 → Работа
user 1 → Работа
```

Это как раз буквально выражает бизнес-правило из ТЗ.

---

# 3. `tasks`

ТЗ задаёт:

* дату создания;
* статус `0/1`, по умолчанию `0`;
* название;
* прикреплённый файл;
* срок;
* автора;
* проект.

Получаем:

```sql
CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status TINYINT(1) NOT NULL DEFAULT 0,
  name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255),
  deadline DATE,
  user_id INT NOT NULL,
  project_id INT NOT NULL,

  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

Здесь:

```text
status
0 → задача не выполнена
1 → задача выполнена
```

`deadline` допускает `NULL`, потому что в форме срок выполнения необязательный. Файл тоже необязательный.

---

# 4. Индексы

В задании специально сказано:

> поля с уникальными значениями → `UNIQUE`;
> поля, по которым будет поиск → индекс.

У нас уже есть:

```sql
email UNIQUE
```

и:

```sql
UNIQUE (user_id, name)
```

для проектов.

Но есть ещё поиск задач:

> искать в таблице задач по имени задачи через `MATCH ... AGAINST`.

Для такого поиска нам понадобится:

```sql
FULLTEXT (name)
```

Кроме того, приложение постоянно выбирает задачи по сроку: сегодня, завтра, просроченные, а уведомления ищут невыполненные задачи на текущую дату.

Поэтому индекс на:

```sql
deadline
```

здесь тоже имеет смысл.

---

# 5. Итоговый `schema.sql`

Я бы для задания сделал его таким:

```sql
DROP DATABASE IF EXISTS doingsdone;

CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;


CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  name VARCHAR(128) NOT NULL,
  password VARCHAR(255) NOT NULL
);


CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,

  UNIQUE (user_id, name),

  FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status TINYINT(1) NOT NULL DEFAULT 0,
  name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255),
  deadline DATE,
  user_id INT NOT NULL,
  project_id INT NOT NULL,

  FULLTEXT (name),
  INDEX (deadline),

  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

Это уже напрямую соответствует сущностям и ограничениям ТЗ.

---

# 6. Структура проекта

Получится:

```text
htmlacademy-php-01-doingsdone/
│
├── compose.yaml
├── schema.sql
│
├── index.php
├── functions.php
├── templates/
├── css/
├── img/
└── ...
```

А когда дойдёшь до следующего задания, скорее всего появится:

```text
queries.sql
```

и станет:

```text
compose.yaml   → как запустить MySQL
schema.sql     → как выглядит БД
queries.sql    → начальные данные и SQL-запросы
PHP            → использует эти запросы
```

---

# 7. Первый запуск

В терминале корня DoingsDone:

```bash
docker compose up -d
```

Проверяем:

```bash
docker compose ps
```

Ожидаем примерно:

```text
doingsdone-mysql   mysql:8.0   Up   0.0.0.0:3308->3306/tcp
```

После этого выполняешь **весь `schema.sql`** через PhpStorm.

Либо CLI:

```bash
mysql -h 127.0.0.1 -P 3308 -u root -p < schema.sql
```

Пароль:

```text
root
```

Проверяем:

```sql
USE doingsdone;

SHOW TABLES;
```

Должно быть:

```text
projects
tasks
users
```

---

# 8. Подключение PhpStorm Database Tool

Создаёшь новый **MySQL Data Source**:

```text
Name:     DoingsDone
Host:     127.0.0.1
Port:     3308
User:     root
Password: root
Database: doingsdone
```

И тогда у тебя справа будет уже два абсолютно независимых проекта:

```text
Database

YetiCave
└── localhost:3307
    └── yeticave
        ├── bets
        ├── categories
        ├── lots
        └── users

DoingsDone
└── localhost:3308
    └── doingsdone
        ├── projects
        ├── tasks
        └── users
```

---

# 9. Как каждый день работать

Для **DoingsDone** начал работу:

```bash
docker compose up -d
```

Закончил:

```bash
docker compose stop
```

Данные остаются в:

```text
doingsdone_mysql_data
```

На следующий день снова:

```bash
docker compose up -d
```

И продолжаешь с того же места.

Сам `schema.sql` **каждый день запускать не надо**. Он нужен, когда ты впервые создаёшь структуру или сознательно хочешь снести тестовую БД и получить чистую.

Таким образом, задание **4.9** у тебя фактически будет состоять из двух файлов:

```text
compose.yaml   ← наша современная инфраструктура, Академия его не требует
schema.sql     ← непосредственно решение задания 4.9
```

Причём `compose.yaml` — это уже наша добавка для удобной современной разработки, а требование HTML Academy непосредственно относится к `schema.sql`: создать базу, таблицы для всех сущностей, первичные ключи, нужные уникальные/поисковые индексы и убедиться, что файл выполняется без ошибок. 

### Конфигурация PHPStorm Database

Да, это нормально. PhpStorm сейчас просит **создать подключение к твоему MySQL**.

Для нашего Docker-контейнера `doingsdone-mysql` заполни так:

| Поле         | Что указать  |
| ------------ | ------------ |
| **Name**     | `DoingsDone` |
| **Host**     | `127.0.0.1`  |
| **Port**     | `3308`       |
| **User**     | `root`       |
| **Password** | `root`       |
| **Database** | `doingsdone` |

То есть главное здесь — **порт `3308`**, потому что:

```text
Windows MySQL → 3306
YetiCave Docker → 3307
DoingsDone Docker → 3308
```

### Если PhpStorm показывает `URL`

Можно выбрать вкладку/режим **General** и заполнить именно отдельные поля.

Должно получиться примерно:

```text
Host:     127.0.0.1
Port:     3308
User:     root
Password: root
Database: doingsdone
```

Нажми **Test Connection**.

Если попросит скачать **MySQL JDBC Driver** — соглашайся, PhpStorm сам его скачает.

После успешного подключения уже можно открывать `schema.sql` и выполнять **весь файл целиком**.

Если хочешь, можешь **скинуть скрин этого окна PhpStorm**, и я прямо по нему скажу, что в каждое поле поставить.
