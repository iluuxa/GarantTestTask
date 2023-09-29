<?php

namespace App;

use Doctrine\ORM\EntityManager;

class DBManager
{
    private static EntityManager $manager;

    public static function start(EntityManager $entityManager): EntityManager{
        self::$manager = $entityManager;
        return self::$manager;

    }

    public static function getManager(): EntityManager
    {
        return self::$manager;
    }

}