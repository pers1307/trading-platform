# Торговая платформа. Система учета финансов

* Разработка строго по TDD
* Более 100 тестов
* Докеризация
* php 8.3
* Symfony 6.4
* AdminLTE 3.2.0
* Интеграция с Telegramm

## При разработке

Запуск сервера внутри контейнера

```bash
symfony serve -d
```

## Тестирование

```bash
./test.sh
```

## Telegramm

[Ссылка на пакет](https://github.com/TelegramBot/Api)

Получить сведения о последних сообщениях в чатах

```
https://api.telegram.org/bot<Token>/getUpdates
```

Получить сведения о самом боте

```
https://api.telegram.org/bot<Token>/getMe
```
