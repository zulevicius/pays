<?php

namespace App\Repository;


use App\Exception\InvalidArgumentException;
use App\Exception\RemoteServiceException;

class ExchangeRatesProvider extends Provider
{

    private const URL_PROP = 'exchange_rates_provider_url';

    private const METHOD_PROP = 'exchange_rates_provider_method';

    public function __construct()
    {
        parent::__construct(self::URL_PROP, self::METHOD_PROP);
    }

    /**
     * @param string $currency
     *
     * @throws InvalidArgumentException
     *
     * @return null|float
     */
    public function getCurrencyExchangeRate(string $currency): float
    {
        $method = $this->getProvidingMethod();
        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException("Currency exchange rate method `$method` does not exist");
        }
        return $this->$method($currency);
    }

    /**
     * @param string $currency
     *
     * @throws RemoteServiceException
     *
     * @return float
     */
    private function getCurrencyEurRateFromService(string $currency): float
    {
        if ($currency === 'EUR') {
            return 1;
        }
        $contents = $this->getRemoteFileContents();
        if ($contents === false) {
            throw new RemoteServiceException('Exchange rates service unreachable');
        }
        $exchangeRates = json_decode($contents, true);
        if (isset($exchangeRates['rates'])) {
            if (isset($exchangeRates['rates'][$currency])) {
                return $exchangeRates['rates'][$currency];
            }
        }
        throw new RemoteServiceException("`$currency` exchange rate not found");
    }
}