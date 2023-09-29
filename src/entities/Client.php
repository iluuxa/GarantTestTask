<?php

namespace App\entities;

use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name='clients')
 */
class Client
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
     * @Column(name='`phone`', type='string')
     */
    private string|null $phone;
    /**
     * @Column(name='`taxpayer_number`',type='string')
     */
    private string $taxpayerNumber;
    /**
     * One product has many features. This is the inverse side.
     * @var Collection<int, Contract>
     * @OneToMany(targetEntity="Contract", mappedBy="clients")
     */
    private Collection $contracts;

}