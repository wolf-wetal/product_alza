# Product_alza

Парсер информации с сайта alza.cz

## Установка

Важно, разаботка велась на `Windows 11, PHP 8.0, MySQL - 5.6`

Инструкции по установке вашего проекта.

1. Склонируйте репозиторий: `git clone https://github.com/wolf-wetal/product_alza.git`
2. Установите зависимости: `composer install`
3. Создайте базу данных: `php bin/console doctrine:database:create` или в ручную
4. Настройне соединение с БД в .env
5. Выполните миграции: `php bin/console doctrine:migrations:migrate`
6. Запустите локальный сервер: `symfony server:start`

## Использование

После установки открываем наш проект в браузере (обычно url: http://127.0.0.1:8000) 
Откроется список товаров (Будет пустым). Нажимем на кнопку "Новый продукт", вводим URL товара.

## Тестирование
Тест лежит в `tests/AlzaParserTest.php`
Можно  запусить в PHPStorm
или же командой:
php vendor/bin/phpunit
