## Запустить контейнеры
`docker-compose up -d`

## в докере:

### Скопировать .env файл

`docker exec organization_exercise_app cp .env.example .env`

### Сгенерировать ключ шифрования

`docker exec organization_exercise_app php artisan key:generate`

### Установить свой API ключ в .env файле. Параметр API_KEY

Можно оставить текущий.

### Мигрировать базу

`docker exec organization_exercise_app php artisan migrate`

### Заполнить базу тестовыми данными

`docker exec organization_exercise_app php artisan db:seed`

### Приложение

http://localhost:8000

### Документация

http://localhost:80000/api//documentation
