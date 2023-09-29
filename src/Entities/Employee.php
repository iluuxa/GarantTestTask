<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\Collection;

class Employee
{
    private int $id;
    private string|null $name;
    private DateTime|null $birthDate;
    private string $salary;
    private array $contracts;

    /**
     * @param int $id
     * @param string|null $name
     * @param DateTime|null $birthDate
     * @param string $salary
     */
    public function __construct(int $id, ?string $name, ?DateTime $birthDate, string $salary)
    {
        $this->id = $id;
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->salary = $salary;
    }


}