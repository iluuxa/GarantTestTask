<?php

namespace App\Entities;

class Client
{
    private int $id;
    private string|null $name;
    private string|null $phone;
    private string $taxpayerNumber;
    private array $contracts;

    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $phone
     * @param string $taxpayerNumber
     */
    public function __construct(int $id, ?string $name, ?string $phone, string $taxpayerNumber)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->taxpayerNumber = $taxpayerNumber;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getTaxpayerNumber(): string
    {
        return $this->taxpayerNumber;
    }

    public function setTaxpayerNumber(string $taxpayerNumber): void
    {
        $this->taxpayerNumber = $taxpayerNumber;
    }

    public function getContracts(): array
    {
        return $this->contracts;
    }

    public function setContracts(array $contracts): void
    {
        $this->contracts = $contracts;
    }

}