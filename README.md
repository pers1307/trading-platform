# Торговая платформа. Система учета финансов

* Разработка строго по TDD
* Более 130 тестов
* Докеризация
* php 8.3.14
* Xdebug 3.3.2
* Symfony 6.4
* AdminLTE 3.2.0
* Интеграция с Telegramm
* Интеграция с Finam API

## Тестирование

```bash
./test.sh
```

## CRON задачи
```bash
# Обновление цен акций
0 13-22 * * 1-5 cd /home/www && /usr/bin/php bin/console app:sync-dictionaries

# Проверка соблюдения рисков в позициях
*/10 10-22 * * 1-5 cd /home/www && /usr/bin/php bin/console app:risk-trade-check

# Проверить состояние открытых позиций
10 14,22 * * 1-5 cd /home/www && /usr/bin/php bin/console app:check-open-trades

# Скачать совершенные сделки
*/5 * * * * cd /home/www && /usr/bin/php bin/console app:dowload-deals

# Обновлять состояние счетов
0 15 * * 1-5 cd /home/www && /usr/bin/php bin/console app:update-accaunt-history
```

## Telegram

[Ссылка на пакет](https://github.com/TelegramBot/Api)

Получить сведения о последних сообщениях в чатах

```
https://api.telegram.org/bot<Token>/getUpdates
```

Получить сведения о самом боте

```
https://api.telegram.org/bot<Token>/getMe
```
