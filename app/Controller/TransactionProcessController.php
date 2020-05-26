<?php

namespace App\Controller;


use App\Entity\Transaction;
use App\Exception\InvalidArgumentException;
use App\Exception\RemoteServiceException;
use App\Repository\BinProvider;
use App\Repository\ExchangeRatesProvider;
use App\Repository\TransactionsReader;

class TransactionProcessController
{
    /**
     * @var BinProvider
     */
    private $binProvider;

    /**
     * @var ExchangeRatesProvider
     */
    private $exchangeRatesProvider;

    public function __construct()
    {
        $this->binProvider = new BinProvider();
        $this->exchangeRatesProvider = new ExchangeRatesProvider();
    }

    /**
     * @param array $args CLI arguments array
     *
     * @throws InvalidArgumentException
     */
    public function printCommissions(array $args): void
    {
        if (!isset($args[1])) {
            throw new InvalidArgumentException('Input file not provided');
        }
        $records = (new TransactionsReader())
            ->readRecords($args[1]);
        foreach ($records as $record) {
            echo $this->getCommissions($record) . "\n";
        }
    }

    /**
     * @param Transaction $t
     *
     * @return string|float
     */
    function getCommissions(Transaction $t)
    {
        $fixedAmount = $t->getAmount();
        if ($t->getCurrency() !== 'EUR') {
            $rate = $this->getCurrencyExchangeRate($t->getCurrency());
            if ($rate === null) {
                return 'Cannot get `' . $t->getCurrency() . '` exchange rate';
            } elseif ($rate !== .0) {
                $fixedAmount /= $rate;
            }
        }

        $isBinEu = $this->isBinFromEu($t->getBin());
        if ($isBinEu === null) {
            return 'Cannot get BIN `' . $t->getBin() . '` info';
        }
        $commissions = ceil($fixedAmount * ($isBinEu ? 0.01 : 0.02) * 100) / 100;
        return $commissions;
    }

    /**
     * @param string $bin
     *
     * @return null|bool
     */
    function isBinFromEu(string $bin): ?bool
    {
        try {
            $isBinEu = $this->binProvider->isBinFromEu($bin);
        } catch (RemoteServiceException $e) {
            return null;
        }
        return $isBinEu;
    }

    /**
     * @param string $currency
     *
     * @return null|float
     */
    function getCurrencyExchangeRate(string $currency): ?float
    {
        try {
            $rate = $this->exchangeRatesProvider->getCurrencyEurRate($currency);
        } catch (RemoteServiceException $e) {
            return null;
        }
        return $rate;
    }
}