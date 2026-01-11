# Task Manager API

REST API для управления задачами, построенная на Symfony 7.4.

## Технологии

- **Symfony 7.4** - PHP фреймворк
- **Doctrine ORM** - Работа с базой данных
- **MySQL 9.1.0** - База данных
- **Docker & Docker Compose** - Контейнеризация
- **Nginx** - Веб-сервер
- **PHP 8.4** - Язык программирования

## Установка и запуск

### 1. Установка зависимостей

```bash
composer install
```

### 2. Настройка окружения

Подключение к базе данных:

```env
DATABASE_URL="mysql://root:root@mysql:3306/tz?serverVersion=9.1.0&charset=utf8mb4"
```

### 3. Запуск через Docker Compose

Запустите все сервисы (PHP, Nginx, MySQL):

```bash
docker compose up -d --build
```

Это запустит:
- **PHP-FPM** контейнер с PHP 8.4
- **Nginx** на порту `8000`
- **MySQL** на порту `3306`

### 4. Выполнение миграций

После запуска контейнеров выполните миграции базы данных:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## Остановка проекта

```bash
docker compose down
```

## API Роуты

Запросы отправляются по адресу http://localhost:8000
### 1. Создание задачи

**POST** `/tasks`

Создает новую задачу.

**Заголовки:**
```
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "title": "Название задачи",
  "description": "Описание задачи",
  "status": "new"
}
```

**Параметры:**
- `title` (string, обязательный) - Название задачи
- `description` (string, обязательный, макс. 255 символов) - Описание задачи
- `status` (string, обязательный) - Статус задачи. Допустимые значения:
  - `new` - Новая
  - `pending` - В ожидании
  - `active` - Активная
  - `inactive` - Неактивная
  - `deleted` - Удаленная

**Пример успешного ответа (200 OK):**
```json
{
  "success": true,
  "resultCode": 200,
  "message": null,
  "data": {
    "id": 1,
    "title": "Название задачи",
    "description": "Описание задачи",
    "status": "new",
    "createdAt": "2025-01-08T22:02:36+03:00",
    "updatedAt": null
  }
}
```

**Пример ошибки валидации (400 Bad Request):**
```json
{
  "success": false,
  "resultCode": 400,
  "message": "Title cannot be empty",
  "data": null
}
```

---

### 2. Получение списка задач

**GET** `/tasks`

Возвращает список всех задач.

**Пример успешного ответа (200 OK):**
```json
{
  "success": true,
  "resultCode": 200,
  "message": null,
  "data": [
    {
      "id": 1,
      "title": "Название задачи",
      "description": "Описание задачи",
      "status": "new",
      "createdAt": "2025-01-08T22:02:36+03:00",
      "updatedAt": null
    },
    {
      "id": 2,
      "title": "Другая задача",
      "description": "Описание",
      "status": "active",
      "createdAt": "2025-01-08T22:05:12+03:00",
      "updatedAt": "2025-01-08T22:10:45+03:00"
    }
  ]
}
```

---

### 3. Получение задачи по ID

**GET** `/tasks/{id}`

Возвращает информацию о конкретной задаче.

**Параметры пути:**
- `id` (integer, обязательный) - ID задачи

**Пример успешного ответа (200 OK):**
```json
{
  "success": true,
  "resultCode": 200,
  "message": null,
  "data": {
    "id": 1,
    "title": "Название задачи",
    "description": "Описание задачи",
    "status": "new",
    "createdAt": "2025-01-08T22:02:36+03:00",
    "updatedAt": null
  }
}
```

**Пример ошибки (404 Not Found):**
```json
{
  "success": false,
  "resultCode": 404,
  "message": "Task not found",
  "data": null
}
```

---

### 4. Обновление задачи

**PUT** `/tasks/{id}`

Обновляет существующую задачу.

**Параметры пути:**
- `id` (integer, обязательный) - ID задачи

**Заголовки:**
```
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "title": "Обновленное название",
  "description": "Обновленное описание",
  "status": "active"
}
```

**Параметры:**
- `title` (string, обязательный) - Название задачи
- `description` (string, обязательный, макс. 255 символов) - Описание задачи
- `status` (string, обязательный) - Статус задачи (см. допустимые значения в разделе "Создание задачи")

**Пример успешного ответа (200 OK):**
```json
{
  "success": true,
  "resultCode": 200,
  "message": null,
  "data": {
    "id": 1,
    "title": "Обновленное название",
    "description": "Обновленное описание",
    "status": "active",
    "createdAt": "2025-01-08T22:02:36+03:00",
    "updatedAt": "2025-01-08T22:15:30+03:00"
  }
}
```

**Пример ошибки (404 Not Found):**
```json
{
  "success": false,
  "resultCode": 404,
  "message": "Task not found",
  "data": null
}
```

---

### 5. Удаление задачи

**DELETE** `/tasks/{id}`

Удаляет задачу по ID.

**Параметры пути:**
- `id` (integer, обязательный) - ID задачи

**Пример успешного ответа (200 OK):**
```json
{
  "success": true,
  "resultCode": 200,
  "message": "Task deleted successfully",
  "data": null
}
```

**Пример ошибки (404 Not Found):**
```json
{
  "success": false,
  "resultCode": 404,
  "message": "Task not found",
  "data": null
}
```

## Формат ответов API

Все ответы API имеют единый формат:

```json
{
  "success": boolean,
  "resultCode": number,
  "message": string | null,
  "data": any | null
}
```

- `success` - Успешность операции (true/false)
- `resultCode` - HTTP код ответа
- `message` - Сообщение (может быть null для успешных операций)
- `data` - Данные ответа (может быть null для ошибок)

## Коды статусов HTTP

- `200 OK` - Успешный запрос
- `400 Bad Request` - Ошибка валидации данных
- `404 Not Found` - Ресурс не найден
- `500 Internal Server Error` - Внутренняя ошибка сервера

### Запуск тестов

```bash
docker compose exec php php bin/phpunit
```

