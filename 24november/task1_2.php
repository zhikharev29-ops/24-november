<?php
declare(strict_types=1);

// Задача 1.2. Система сотрудников

// Employee абстрактный, поэтому создать его напрямую нельзя: new Employee(...) даст Error.
// Класс описывает только то, что общее у всех: имя и оклад. А как считать зарплату, он не
// знает, у каждой должности своя формула, и calculateSalary() здесь только объявлен, без кода.
// Объект, у которого метод есть, а кода у него нет, создавать нельзя, поэтому PHP это запрещает.
abstract class Employee
{
    protected string $name;
    protected float $baseSalary;

    public function __construct(string $name, float $baseSalary)
    {
        $this->name = $name;
        $this->baseSalary = $baseSalary;
    }

    abstract public function calculateSalary(): float;

    public function getName(): string
    {
        return $this->name;
    }
}

class Manager extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 1.2; // оклад + 20% премия
    }
}

class Developer extends Employee
{
    private int $overtimeHours = 0;

    public function setOvertimeHours(int $hours): void
    {
        $this->overtimeHours = $hours;
    }

    public function calculateSalary(): float
    {
        return $this->baseSalary + $this->overtimeHours * 500;
    }
}

class Intern extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 0.5;
    }
}

function money(float $x): string
{
    return number_format($x, 2, ',', ' ') . ' руб.';
}

// str_pad не умеет считать кириллицу, поэтому свой вариант
function pad(string $s, int $width): string
{
    return $s . str_repeat(' ', max(0, $width - preg_match_all('/./us', $s)));
}

function printPayroll(array $employees): void
{
    $total = 0.0;
    echo pad('Имя', 18) . '— Зарплата' . PHP_EOL;
    echo str_repeat('-', 40) . PHP_EOL;
    foreach ($employees as $employee) {
        $salary = $employee->calculateSalary();
        $total += $salary;
        echo pad($employee->getName(), 18) . '— ' . money($salary) . PHP_EOL;
    }
    echo str_repeat('-', 40) . PHP_EOL;
    echo pad('Общий фонд', 18) . '— ' . money($total) . PHP_EOL;
}

// ---------- Демонстрация ----------
$manager = new Manager('Анна Смирнова', 100000);

$developer = new Developer('Олег Иванов', 90000);
$developer->setOvertimeHours(10);

$intern = new Intern('Мария Петрова', 40000);

printPayroll([$manager, $developer, $intern]);
