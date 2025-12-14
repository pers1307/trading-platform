# План функционала отслеживания счетов с поправкой на инфляцию

## Цель
- Отображать динамику счетов (`accaunt`) в реальных ценах, сравнивая номинальные значения с инфляционно-скорректированными.

## Основные допущения
- Источник инфляции: ключевая ставка из `\App\Service\CentralBankKeyRateService` (данные ЦБ), формат — последняя доступная ставка.
- Корректировка делается в национальной валюте (RUB). Мультивалютность пока не учитывается.
- Для тестов допускается фиктивный загрузчик, подсовывающий статичные данные.

## План реализации (итеративно)
1) Модель данных
   - Новая таблица `accaunt_inflation` для хранения срезов счетов с учетом инфляции.
   - Поля и типы:
     - `id` BIGINT UNSIGNED PK AUTO_INCREMENT.
     - `accaunt_id` BIGINT UNSIGNED NOT NULL — FK на `accaunt.id`.
     - `date` DATE NOT NULL — дата среза (обычно конец месяца).
     - `movement_amount` DOUBLE NOT NULL DEFAULT 0 — суммарное движение средств за период (пополнения/снятия).
     - `central_bank_key_rate` DECIMAL(5,2) NOT NULL — ключевая ставка ЦБ на дату, % годовых.
     - `accaunt_inflation_balance` DOUBLE NOT NULL — баланс, скорректированный на инфляцию (реальная стоимость).
     - `deposit_rate` DECIMAL(5,2) NOT NULL — ставка по вкладу, % годовых (для сравнения).
     - `accaunt_deposit_balance` DOUBLE NOT NULL — баланс при эквивалентном размещении под ставку вклада.
     - `accaunt_balance` DOUBLE NOT NULL — номинальный баланс счета на дату.
   - Индексы: UNIQUE (`accaunt_id`, `date`) для единственного среза; INDEX (`date`) для выборок по периоду; FK на `accaunt`.
   - Миграция Doctrine: создать таблицу, индексы, FK; добавить фикстуры с историческими значениями для тестов.
   - Сущность `AccauntInflation` + репозиторий для выборок по аккаунту/периоду.
2) Получение данных
   - Сервис получения ключевой ставки ЦБ: `\App\Service\CentralBankKeyRateService` делает HTTP-запрос на https://www.cbr.ru/hd_base/keyrate/, парсит последнее значение ставки; покрыть unit тестом с фиктивным HTML. Корректировки для нестандартных периодов не требуются.
3) Доменные сервисы
   - Расширить слой работы со счетами: `AccauntInflationCalculator::calculate(AccauntInflationInputDto)` вызывает `\App\Service\PercentCalculator\PercentCalculator::calculate` отдельно для `accaunt_inflation_balance` и `accaunt_deposit_balance`; возвращает `AccauntInflationResult` с обновленными значениями этих балансов и производными полями.
7) Консольная команда расчета (`inflation:calculate`)
   - Команда `inflation:calculate` принимает параметры `movement_amount`, `accaunt_id`, `date` (обязательная дата среза в формате Y-m-d).
   - Шаги выполнения:
     1. Валидировать дату, загрузить счет по `accaunt_id`.
     2. Собрать `AccauntInflationInputDto` (accaunt, movement_amount, date) и передать в `AccauntInflationCalculator::calculate`.
     3. Калькулятор берет последний баланс из `AccauntHistoryRepository`, добавляет движение, подтягивает текущую ставку ЦБ (`CentralBankKeyRateService`), считает инфляционный/депозитный балансы.
     4. Результат (`AccauntInflationResult`) сохраняется в `accaunt_inflation` upsert-ом по (`accaunt_id`, `date`).
   - Покрыть unit тестами DTO/сервиса и functional/интеграционным тестом команды (ветки: корректная дата, неверная дата, счет не найден, новый/существующий срез).

## Открытые вопросы
- В отчетности выводим индекс, опираясь на последнее значение из `AccauntInflationRepository` по `accaunt_id`.
