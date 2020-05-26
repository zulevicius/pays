<?php

use App\Controller\TransactionProcessController;
use App\Entity\Transaction;
use PHPUnit\Framework\TestCase;

final class TransactionProcessControllerTest extends TestCase
{
    public function testGetCommissions_EuCardEur(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(true);
        $stub->method('getCurrencyExchangeRate')->willReturn(1.2);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('EUR')
            ->setBin('bin');
        $this->assertSame((float)1, $stub->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardEur(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(false);
        $stub->method('getCurrencyExchangeRate')->willReturn(1.2);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('EUR')
            ->setBin('bin');
        $this->assertSame((float)2, $stub->getCommissions($transaction));
    }

    public function testGetCommissions_EuCardUsd(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(true);
        $stub->method('getCurrencyExchangeRate')->willReturn(1.3);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)0.77, $stub->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardUsd(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(false);
        $stub->method('getCurrencyExchangeRate')->willReturn(1.3);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)1.54, $stub->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardUsdExchangeRateZero(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(false);
        $stub->method('getCurrencyExchangeRate')->willReturn(0.0);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)2, $stub->getCommissions($transaction));
    }

    public function testGetCommissions_ExceptionExchangeRate(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(false);
        $stub->method('getCurrencyExchangeRate')->willReturn(null);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame('Cannot get `USD` exchange rate', $stub->getCommissions($transaction));
    }

    public function testGetCommissions_ExceptionBin(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['getCommissions', 'printCommissions'])
            ->getMock();
        $stub->method('isBinFromEu')->willReturn(null);
        $stub->method('getCurrencyExchangeRate')->willReturn(1.0);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame('Cannot get BIN `bin` info', $stub->getCommissions($transaction));
    }
}
