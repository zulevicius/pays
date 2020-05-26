<?php

namespace App\Repository;


use App\Exception\RemoteServiceException;

class BinProvider extends UrlProvider
{
    private const EU_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];

    private const URL_PROP = 'bin_provider';

    function __construct()
    {
        parent::__construct(self::URL_PROP);
    }

    /**
     * @param string $bin
     *
     * @return bool
     */
    public function isBinFromEu(string $bin): bool
    {
        return in_array($this->getBinCountry($bin), self::EU_COUNTRIES);
    }

    /**
     * @param string $bin
     *
     * @throws RemoteServiceException
     *
     * @return string
     */
    private function getBinCountry(string $bin): string
    {
        $binContents = file_get_contents($this->getUrl() . '/' . $bin);
        if ($binContents === false) {
            throw new RemoteServiceException("BIN `$bin` not found");
        }
        $binData = json_decode($binContents);
        return strtoupper($binData->country->alpha2);
    }
}