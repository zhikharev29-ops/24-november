<?php
declare(strict_types=1);

trait JsonExportTrait
{
    public function export(): string
    {
        // json_encode берёт только публичные свойства
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }
    public function getFormat(): string
    {
        return 'JSON';
    }
}

trait XmlExportTrait
{
    public function export(): string
    {
        $tag = strtolower(static::class);
        $xml = "<$tag>";
        foreach (get_object_vars($this) as $key => $value) {
            $xml .= "<$key>" . htmlspecialchars((string)$value) . "</$key>";
        }
        return $xml . "</$tag>";
    }
    public function getFormat(): string
    {
        return 'XML';
    }
}

trait PriceTrait
{
    public function getPriceWithTax(float $tax): float
    {
        return $this->price * (1 + $tax / 100);
    }
}

class Product
{
    public string $name;
    public float $price;
    // export() и getFormat() есть в обоих трейтах, поэтому PHP требует выбрать,
    // какой метод брать (insteadof), а второй можно оставить под другим именем (as).
    use JsonExportTrait, XmlExportTrait, PriceTrait {
        JsonExportTrait::export insteadof XmlExportTrait;
        XmlExportTrait::export as exportToXml;

        JsonExportTrait::getFormat insteadof XmlExportTrait;
        XmlExportTrait::getFormat as getXmlFormat;
    }
    public function __construct(string $name, float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
    // Свой getFormat() в классе. Теперь $product->getFormat() вернёт «Product format».
    // Приоритет такой: метод самого класса главнее метода из трейта, а метод из трейта
    // главнее унаследованного от родителя. Поэтому getFormat() из JsonExportTrait,
    // который мы выбрали через insteadof, тут перекрыт методом класса и не вызывается.
    public function getFormat(): string
    {
        return 'Product format';
    }
}

// Демонстрация
$product = new Product('Ноутбук', 85000.0);
echo 'JSON: ' . $product->export() . PHP_EOL;
echo 'XML:  ' . $product->exportToXml() . PHP_EOL;
echo PHP_EOL;
echo 'getFormat():    ' . $product->getFormat() . PHP_EOL;
echo 'getXmlFormat(): ' . $product->getXmlFormat() . PHP_EOL;
echo PHP_EOL;
echo 'Цена с налогом 20%: ' . number_format($product->getPriceWithTax(20.0), 2, ',', ' ') . ' руб.' . PHP_EOL;
