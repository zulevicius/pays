<?php

namespace App\Repository;


use App\Exception\InvalidArgumentException;
use App\Exception\RemoteServiceException;

class BinProvider extends Provider
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

    private const URL_PROP = 'bin_provider_url';

    private const METHOD_PROP = 'bin_provider_method';

    public function __construct()
    {
        parent::__construct(self::URL_PROP, self::METHOD_PROP);
    }

    /**
     * @param string $bin
     *
     * @throws InvalidArgumentException
     *
     * @return null|bool
     */
    public function isBinFromEu(string $bin): bool
    {
        $method = $this->getProvidingMethod();
        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException("Currency exchange rate method `$method` does not exist");
        }
        return in_array($this->$method($bin), self::EU_COUNTRIES);
    }

    /**
     * @param string $bin
     *
     * @throws RemoteServiceException
     *
     * @return string
     */
    private function getBinCountryFromService(string $bin): string
    {
        $binContents = $this->getRemoteFileContents('/' . $bin);
        if ($binContents === false) {
            throw new RemoteServiceException("BIN service unreachable");
        }
        $binData = json_decode($binContents);
        if (isset($binData->country)) {
            if (isset($binData->country->alpha2)) {
                return strtoupper($binData->country->alpha2);
            }
        }
        throw new RemoteServiceException("BIN `$bin` not found");
    }
}