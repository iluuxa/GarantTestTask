<?php

namespace App\Entities;

use DateTime;

class Contract
{
    private int $id;
    private DateTime|null $start;
    private DateTime|null $end;
    private string|null $sum;
    private Employee|null $employee = null;

    /**
     * @param int $id
     * @param DateTime|null $start
     * @param DateTime|null $end
     * @param string|null $sum
     * @param Employee|null $employee
     */
    public function __construct(int $id, ?DateTime $start, ?DateTime $end, ?string $sum, ?Employee $employee)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->sum = $sum;
        $this->employee = $employee;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setStart(?DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): void
    {
        $this->end = $end;
    }

    public function getSum(): ?string
    {
        return $this->sum;
    }

    public function setSum(?string $sum): void
    {
        $this->sum = $sum;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }


}