<?php

namespace App\Repository;


use App\Entity\Transaction;

class TransactionsReader extends FileReader
{

    /**
     * @param string $filename
     *
     * @return array
     */
    public function readRecords(string $filename): array
    {
        $entities = [];
        foreach ($this->getFileByLines($filename) as $line) {
            $transactionPars = json_decode($line);
            $entities[] = (new Transaction())
                ->setBin($transactionPars->bin)
                ->setAmount((float)$transactionPars->amount)
                ->setCurrency($transactionPars->currency);
        }

        return $entities;
    }
}