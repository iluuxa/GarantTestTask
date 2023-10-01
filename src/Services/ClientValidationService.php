<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use Klein\Request;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ClientValidationService extends ValidationService
{
    /**
     * @throws NumberParseException
     * @throws ValidationException
     */
    public function validateClient(Request $request): array
    {
        $array = empty($request->paramsPost()->all()) ? (json_decode($request->body(), true)) : $request->paramsPost()->all();
        $result = [];
        if (isset($array['name'])) {
            $result['name'] = $array['name'];
        }
        if (!empty($array['phone'])) {
            if ($array['phone'][0] == 8) {
                $array['phone'] = substr_replace($array['phone'], '+7', 0, 1);
            }
            /*https://habr.com/ru/articles/279751/*/
            $phoneUtil = PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($array['phone']);
            if (!$phoneUtil->isValidNumber($number)) {
                throw new ValidationException('Invalid phone number: ' . $array['phone']);
            }
            $result['phone'] = $phoneUtil->format($number, PhoneNumberFormat::E164);
        }
        if (isset($array['taxpayer_number'])) {
            if (!ctype_digit($array['taxpayer_number']) ||
                !((mb_strlen($array['taxpayer_number']) == 12) || (mb_strlen($array['taxpayer_number']) == 10))
            ) {
                throw new ValidationException('Invalid taxpayer number: ' . $array['taxpayer_number']);
            }
            $result['taxpayer_number'] = $array['taxpayer_number'];
        }
        return $result;
    }

    /**
     * @throws ValidationException
     */
    public function validateClientId(Request $request): int
    {
        if (intval($request->id) <= 0) {
            throw new ValidationException('Неверный id');
        } else {
            return intval($request->id);
        }
    }

    public function validateGetParams(array $params): array
    {
        $result = [];
        if ($params['sort'] == 0) {
            $result['sort'] = 0;
        } else {
            $result['sort'] = 1;
        }
        $result['limit'] = intval($params['limit']);
        $result['by'] = strval($params['by']);
        return $result;
    }

}