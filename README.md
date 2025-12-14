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

## Расчет инфляционных показателей счета

Консольная команда `inflation:calculate` пересчитывает баланс счета с учетом инфляции и альтернативного депозита и сохраняет срез в таблицу `accaunt_inflation` (upsert по `accaunt_id` + `date`).

Аргументы:
- `movement_amount` — движение средств за период (пополнение/вывод).
- `accaunt_id` — идентификатор счета.
- `date` — дата среза в формате `YYYY-MM-DD`.

Примеры (docker-compose):
```bash
# Рассчитать срез для счета 1 на 2025-12-14 с движением +100
docker compose run --rm php php bin/console inflation:calculate 100 1 2025-12-14

# Рассчитать срез для счета 2 на сегодня (пример даты)
docker compose run --rm php php bin/console inflation:calculate 0 2 $(date +%F)
```
