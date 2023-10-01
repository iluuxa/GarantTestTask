<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use DateTime;

class ValidationService
{
    /**
     * @throws ValidationException
     */
    protected function validateDate(string $date): DateTime
    {
        $result = DateTime::createFromFormat(DATE_FORMAT, $date);
        if (!$date) {
            throw new ValidationException('Некорректная дата: ' . $date . '. Корректный формат даты: ГГГГ-ММ-ДД.');
        }
        $result = $result->setTime(0, 0, 0, 0);
        if ($result->format(DATE_FORMAT) != $date) {
            throw new ValidationException('Некорректная дата: ' . $date . '. Корректный формат даты: ГГГГ-ММ-ДД.');
        }
        return $result;
    }
}