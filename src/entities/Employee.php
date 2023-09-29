<?php

namespace App\entities;

use DateTime;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name='employees')
 */
class Employee
{
    /**
     * @Id
     * @Column(type='integer')
     * @GeneratedValue(strategy='AUTO')
     */
    private int $id;
    /**
     * @Column(name='`name`',type='string')
     */
    private string|null $name;
    /**
     * @Column(name='`birth_date`', type='datetime')
     */
    private DateTime|null $birthDate;
    /**
     * @Column(name='`salary`',type='decimal')
     */
    private string $salary;
    /**
     * @var Collection<int, Contract>
     * @OneToMany(targetEntity="Contract", mappedBy="employees")
     */
    private Collection $contracts;

}