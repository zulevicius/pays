<?php

namespace App\Controller;


use App\Entity\Transaction;
use App\Exception\InvalidArgumentException;
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

    /**
     * @param BinProvider $binProvider
     * @param ExchangeRatesProvider $exchangeRatesProvider
     */
    public function __construct(BinProvider $binProvider, ExchangeRatesProvider $exchangeRatesProvider)
    {
        $this->binProvider = $binProvider;
        $this->exchangeRatesProvider = $exchangeRatesProvider;
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
            $rate = $this->exchangeRatesProvider->getCurrencyExchangeRate($t->getCurrency());
            if ($rate !== .0) {
                $fixedAmount /= $rate;
            }
        }

        $isBinEu = $this->binProvider->isBinFromEu($t->getBin());
        $commissions = ceil($fixedAmount * ($isBinEu ? 0.01 : 0.02) * 100) / 100;

        return $commissions;
    }
}