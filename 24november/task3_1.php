<?php
declare(strict_types=1);

// Задача 3.1. Логирование через трейт

trait LoggerTrait
{
    private array $logs = [];

    public function log(string $message): void
    {
        $this->logs[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }
}

// Трейт здесь удобнее наследования по двум причинам.
// 1) UserService и OrderService никак не связаны по смыслу, одного родителя
//    у них нет, и придумывать для них общий класс только ради логов неправильно.
// 2) Наследовать можно только от одного класса. Если бы логирование сидело
//    в родителе, этот единственный родитель был бы занят, а трейтов можно
//    подключить сколько угодно.
// Код логирования написан один раз, а у каждого класса получается свой массив $logs,
// поэтому журналы сервисов друг с другом не смешиваются.

class UserService
{
    use LoggerTrait;

    public function register(string $username): void
    {
        // здесь могла бы быть запись в базу
        $this->log("Зарегистрирован пользователь $username");
    }
}

class OrderService
{
    use LoggerTrait;

    public function createOrder(int $orderId, float $total): void
    {
        $this->log("Создан заказ №$orderId на сумму " . number_format($total, 2, ',', ' ') . ' руб.');
    }
}

function printLogs(string $title, array $logs): void
{
    echo $title . ':' . PHP_EOL;
    if ($logs === []) {
        echo '  (пусто)' . PHP_EOL;
    }
    foreach ($logs as $line) {
        echo '  ' . $line . PHP_EOL;
    }
}

// ---------- Демонстрация ----------
$users = new UserService();
$users->register('anna');
$users->register('oleg');
$users->register('maria');

$orders = new OrderService();
$orders->createOrder(101, 2500.0);
$orders->createOrder(102, 14990.5);

printLogs('Журнал UserService', $users->getLogs());
printLogs('Журнал OrderService', $orders->getLogs());

echo PHP_EOL . 'Очищаем журнал UserService...' . PHP_EOL . PHP_EOL;
$users->clearLogs();

printLogs('Журнал UserService', $users->getLogs());
printLogs('Журнал OrderService', $orders->getLogs());
