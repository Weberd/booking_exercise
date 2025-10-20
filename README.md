# Установка

## Запустить контейнер
`docker-compose up -d`

### Скопировать .env файл

`cp .env.example .env`

### Сгенерировать ключ шифрования

`php artisan key:generate`

### Создать базу

Зайти в контейнер mysql

`docker exec -it booking_system_db mysql -u root -p`

пароль: `root`

```
# MySQL
CREATE DATABASE booking_service CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel'@'%' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON booking_service.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
```

### Мигрировать базу

`php artisan migrate`

### Заполнить базу тестовыми данными

`php artisan db:seed`

### Запуск бекенда

`php artisan serv`

### Запуск фронтенда

`npm run dev`

### Приложение

http://localhost:8000

# Структура базы данных

```
┌─────────────────┐         ┌──────────────────┐
│   services      │         │    bookings      │
├─────────────────┤         ├──────────────────┤
│ id (PK)         │◄────────┤ id (PK)          │
│ name            │         │ service_id (FK)  │
│ duration        │         │ customer_name    │
│ created_at      │         │ customer_phone   │
│ updated_at      │         │ booking_date     │
└─────────────────┘         │ start_time       │
                            │ end_time         │
                            │ status           │
                            │ created_at       │
                            │ updated_at       │
                            └──────────────────┘
```

# Обработка Race Condition

### Стратегия: Пессимистическая блокировка с транзакциями

```php
DB::transaction(function () use ($data) {
    // 1. Блокируем строки для проверки (SELECT ... FOR UPDATE)
    $conflictingBookings = Booking::where('service_id', $data['service_id'])
        ->where('booking_date', $data['booking_date'])
        ->where('status', 'active')
        ->lockForUpdate()
        ->get();
    
    // 2. Проверяем временные конфликты
    foreach ($conflictingBookings as $booking) {
        if ($this->timesOverlap(
            $booking->start_time, 
            $booking->end_time,
            $data['start_time'], 
            $data['end_time']
        )) {
            throw new BookingConflictException('Время уже занято');
        }
    }
    
    // 3. Создаем бронирование (другие транзакции будут ждать)
    return Booking::create($data);
});
```

## Проверка на блокировку

Первый curl запускается асинхронно, второй запускается не заврешая первый и возвращает 409 (конфлиакт) 

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "service_id": 1,
    "customer_name": "Клиент А",
    "customer_phone": "+7 111 111-11-11",
    "booking_date": "2025-10-20",
    "start_time": "15:00"
  }' &

curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "service_id": 1,
    "customer_name": "Клиент Б",
    "customer_phone": "+7 222 222-22-22",
    "booking_date": "2025-10-20",
    "start_time": "15:00"
  }'
```
