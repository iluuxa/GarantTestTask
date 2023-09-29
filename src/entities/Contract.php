<?php

namespace App\entities;

use DateTime;

/**
 * @Entity
 * @Table(name='contracts')
 */
class Contract
{
    /**
     * @Id
     * @Column(type='integer')
     * @GeneratedValue(strategy='AUTO')
     */
    private int $id;
    /**
     * @Column(name='`start`',type='datetime')
     */
    private DateTime|null $start;
    /**
     * @Column(name='`end`', type='datetime')
     */
    private DateTime|null $end;
    /**
     * @Column(name='`sum`',type='decimal')
     */
    private string|null $sum;
    /**
     * @ManyToOne(targetEntity="Client", inversedBy="contracts")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private Client|null $client = null;
    /**
     * @ManyToOne(targetEntity="Employee", inversedBy="contracts")
     * @JoinColumn(name="employee_id", referencedColumnName="id")
     */
    private Client|null $employee = null;

}