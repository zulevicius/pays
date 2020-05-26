<?php

namespace App\Repository;


use App\Exception\RemoteServiceException;

class ExchangeRatesProvider extends UrlProvider
{

    private const URL_PROP = 'exchange_rates_provider';

    function __construct()
    {
        parent::__construct(self::URL_PROP);
    }

    /**
     * @param string $currency
     *
     * @throws RemoteServiceException
     *
     * @return float
     */
    public function getCurrencyEurRate($currency): float
    {
        $contents = file_get_contents($this->getUrl());
        if ($contents === false) {
            throw new RemoteServiceException('Exchange rates not found');
        }
        $exchangeRates = json_decode($contents, true);
        return $exchangeRates['rates'][$currency];
    }
}