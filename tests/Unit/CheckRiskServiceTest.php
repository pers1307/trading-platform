<?php

namespace Unit;

use App\Converter\DataToMoexStockConverter;
use App\Service\CheckRiskService;
use PHPUnit\Framework\TestCase;

class CheckRiskServiceTest extends TestCase
{
    private CheckRiskService $checkRiskService;

    public function setUp(): void
    {
        $this->checkRiskService = new CheckRiskService();
    }

    public function tearDown(): void
    {
        unset($this->checkRiskService);
    }




    /**
     * Получить данные
     */

    /**
     * Передать данные
     */

    /**
     * Получить некую структуру для изменения
     */

    /**
     * Применить эти данные в БД
     */

    /**
     * @todo: пробуем зарефакторить на функциональный метод
     * Получение данных - отдельный сервис
     * Обновление данных отдельный сервис
     * Где - то отдельно хранить константы
     * Сделать некий конвертер данных (это пойдет в юнит тесты)
     * Сделать некий обновлятор данных (это пойдет в интеграционные тесты)
     * Предусмотреть единичное обновление цены инструмента
     * стр. 172
     * Возвращает команду создания побочного эффекта, которую он хочет выполнить
     * Разделение бизнес логики и побочных эффектов
     * @todo для будущего модуля рисков нужно ещё отслеживать максимальную, минимальную и прочие цены
     * а не только текущую, иначе детектить позиции будет сложно
     */

}
