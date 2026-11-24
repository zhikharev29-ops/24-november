<?php
declare(strict_types=1);

// Задача 2.1. Способы оплаты

interface PaymentInterface
{
    public function pay(float $amount): bool;
    public function refund(float $amount): bool;
    public function getMethodName(): string;
}

class CreditCard implements PaymentInterface
{
    private float $limit;

    public function __construct(float $limit)
    {
        $this->limit = $limit;
    }

    public function pay(float $amount): bool
    {
        return $amount <= $this->limit; // успешно, пока сумма не больше лимита
    }

    public function refund(float $amount): bool
    {
        return true;
    }

    public function getMethodName(): string
    {
        return 'Кредитная карта';
    }
}

class PayPal implements PaymentInterface
{
    private float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function pay(float $amount): bool
    {
        if ($amount > $this->balance) {
            return false;
        }
        $this->balance -= $amount;
        return true;
    }

    public function refund(float $amount): bool
    {
        $this->balance += $amount;
        return true;
    }

    public function getMethodName(): string
    {
        return 'PayPal';
    }
}

class Cash implements PaymentInterface
{
    public function pay(float $amount): bool
    {
        return true;
    }

    public function refund(float $amount): bool
    {
        return true;
    }

    public function getMethodName(): string
    {
        return 'Наличные';
    }
}

function money(float $x): string
{
    return number_format($x, 2, ',', ' ') . ' руб.';
}

// Функция принимает любой объект, который реализует PaymentInterface.
// Какой именно это класс, ей знать не нужно.
function checkout(PaymentInterface $method, float $amount): void
{
    $ok = $method->pay($amount);
    echo '[' . $method->getMethodName() . '] оплата ' . money($amount) . ': '
        . ($ok ? 'успешно' : 'отказ') . PHP_EOL;
}

// ---------- Демонстрация ----------
echo '--- Кредитная карта (лимит 10 000) ---' . PHP_EOL;
$card = new CreditCard(10000);
checkout($card, 7500);    // проходит
checkout($card, 15000);   // больше лимита, отказ

echo PHP_EOL . '--- PayPal (баланс 5 000) ---' . PHP_EOL;
$paypal = new PayPal(5000);
checkout($paypal, 3000);  // проходит, остаётся 2 000
checkout($paypal, 4000);  // на балансе только 2 000, отказ
$paypal->refund(1000);    // возврат, на балансе снова 3 000
checkout($paypal, 3000);  // теперь проходит

echo PHP_EOL . '--- Наличные ---' . PHP_EOL;
$cash = new Cash();
checkout($cash, 999999);
// Неуспешной оплаты наличными не бывает: по заданию Cash всегда успешен.
