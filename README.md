## Проект: Автопостинг ботом в телеграм
- Проект на Laravel + докер

## Канал проекта
[https://t.me/ByteBreak](https://t.me/ByteBreak/44)

### Запуск локально
1. Скачиваем или клонируем код проекта
2. В linux размещаете код где удобно, в windows лучше зайти в wsl и в нем уже размещать код
3. В директории с кодом набираем cp .env.example .env
4. Создаем бота в телеграм https://t.me/BotFather и токен записываем в TELEGRAM_BOT_CLIENT_TOKEN в файле .env
5. В директории с кодом набираем docker-compose up -d
6. Заходим в php контейнер docker-compose exec php bash
7. Выполняем composer install . ВНИМАНИЕ на проде нужно выполнять composer install --no-dev и APP_DEBUG должен быть false


Проект соберется и запустится.
1. Далее нам нужно создать два канала в телеграм и бота
2. Создаем бота в @BotFather берем токен пота и в .env в TELEGRAM_BOT_CLIENT_TOKEN
3. Добавляем бота в каналы
4. Заходим в php контейнер docker-compose exec php bash
5. Выполняем php artisan telegram:update - там мы будет взвимойдествовать с ботом локально, на сервере будет работать webhook
6. Команду php artisan telegram:update нужно будет перезапускать каждый раз при изменении кода.
7. Публикуем первый пост, далее в базе данных в channels появится запись сменить тип с 1 на 2 - чтоб сохранялись посты в базу
8. Опубликовать второй пост
9. На это пока все возможности проекта )

## Что сделано
- Основной файл откуда стоит смотреть цепочку связи кода \App\Telegram\TelegramHandler
- Бот забирает публикации из канала, где хранятся посты для публикации и сохраняет их в базу. 
- При редактировании постов в канале, посты в базе так же обновляются
- Удалять посты нельзя, нужно их делать не валидными перед удалением. Так как бот не получает события удаляния

## Что нужно сделать?
- Публикации в канал
- Настройки у бота для настроек какой канал источник, какой канал для публикаций
- И остальные настройки
