<?php
declare(strict_types=1);

// Задача 2.2. Форматирование отчётов

interface ReportFormatterInterface
{
    public function format(array $data): string;
}

interface ReportHeaderInterface
{
    const SEPARATOR = '----------------------------------------';

    public function getHeader(): string;
}

class HtmlFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        $html = '<ul>' . PHP_EOL;
        foreach ($data as $item) {
            $html .= '  <li>' . $item . '</li>' . PHP_EOL;
        }
        return $html . '</ul>';
    }
}

class CsvFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        return implode(';', $data);
    }
}

// Один класс реализует сразу два интерфейса
class SalesReport implements ReportFormatterInterface, ReportHeaderInterface
{
    private array $sales;

    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    public function getHeader(): string
    {
        return 'Отчёт о продажах за текущий период';
    }

    // Интерфейс требует параметр $data, но отчёт форматирует свои данные: массив продаж,
    // который он хранит с момента создания.
    public function format(array $data): string
    {
        $lines = [];
        foreach ($this->sales as $product => $sum) {
            $lines[] = 'Продукт: ' . $product . ' — сумма: ' . number_format($sum, 2, ',', ' ') . ' руб.';
        }
        return implode(PHP_EOL, $lines);
    }
}

function renderReport(ReportFormatterInterface $report): void
{
    echo $report->format([]) . PHP_EOL;
}

// Заголовок берём через другой интерфейс
function showHeader(ReportHeaderInterface $report): void
{
    echo $report->getHeader() . PHP_EOL;
}

// ---------- Демонстрация ----------
$items = ['Яблоки', 'Груши', 'Сливы'];

echo 'HTML:' . PHP_EOL;
echo (new HtmlFormatter())->format($items) . PHP_EOL;

echo PHP_EOL . 'CSV:' . PHP_EOL;
echo (new CsvFormatter())->format($items) . PHP_EOL;

$report = new SalesReport([
    'Ноутбук' => 85000.0,
    'Мышь' => 1200.5,
    'Монитор' => 23400.0,
]);

// Один и тот же объект передаём в renderReport() и как источник заголовка
echo PHP_EOL;
showHeader($report);
renderReport($report);

echo PHP_EOL . 'Значение SEPARATOR: ' . ReportHeaderInterface::SEPARATOR . PHP_EOL;
