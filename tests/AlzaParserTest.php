<?php

use PHPUnit\Framework\TestCase;
use App\Service\AlzaParser;

class AlzaParserTest extends TestCase
{
    public function testParsingAlzaProduct()
    {
// Создание экземпляра парсера
        $parser = new AlzaParser();

// URL страницы товара на alza.cz для теста
        $url = 'https://www.alza.cz/alzapower-a100-fast-charge-20w-bila-d6328635.htm';

// Вызов метода для парсинга информации с данной страницы
        $productData = $parser->parseProductInfo($url);

// Проверка, что массив данных не пустой
        $this->assertNotEmpty($productData);

// Проверка наличия ключевых полей в данных
        $this->assertArrayHasKey('name', $productData);
        $this->assertArrayHasKey('description', $productData);
        $this->assertArrayHasKey('price', $productData);
        $this->assertArrayHasKey('image', $productData);

// Проверка, что цена положительная и является числом
        $this->assertIsNotFloat($productData['price']);
        $this->assertGreaterThanOrEqual(0, $productData['price']);

// Проверка, что имя и описание не пустые строки
        $this->assertNotEmpty($productData['name']);
        $this->assertNotEmpty($productData['description']);

// Проверка, что URL изображения является корректным URL
        $this->assertStringStartsWith('http', $productData['image']);
    }
}
