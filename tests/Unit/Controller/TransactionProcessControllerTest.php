<?php

use App\Controller\TransactionProcessController;
use App\Entity\Transaction;
use App\Exception\InvalidArgumentException;
use App\Repository\BinProvider;
use App\Repository\ExchangeRatesProvider;
use PHPUnit\Framework\TestCase;

final class TransactionProcessControllerTest extends TestCase
{
    public function testGetCommissions_EuCardEur(): void
    {
        $stubBin = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubBin->method('isBinFromEu')->willReturn(true);
        $stubRate = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubRate->method('getCurrencyExchangeRate')->willReturn(1.2);
        $tpc = new TransactionProcessController($stubBin, $stubRate);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('EUR')
            ->setBin('bin');
        $this->assertSame((float)1, $tpc->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardEur(): void
    {
        $stubBin = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubBin->method('isBinFromEu')->willReturn(false);
        $stubRate = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubRate->method('getCurrencyExchangeRate')->willReturn(1.2);
        $tpc = new TransactionProcessController($stubBin, $stubRate);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('EUR')
            ->setBin('bin');
        $this->assertSame((float)2, $tpc->getCommissions($transaction));
    }

    public function testGetCommissions_EuCardUsd(): void
    {
        $stubBin = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubBin->method('isBinFromEu')->willReturn(true);
        $stubRate = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubRate->method('getCurrencyExchangeRate')->willReturn(1.3);
        $tpc = new TransactionProcessController($stubBin, $stubRate);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)0.77, $tpc->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardUsd(): void
    {
        $stubBin = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubBin->method('isBinFromEu')->willReturn(false);
        $stubRate = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubRate->method('getCurrencyExchangeRate')
            ->willReturn(1.3);
        $tpc = new TransactionProcessController($stubBin, $stubRate);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)1.54, $tpc->getCommissions($transaction));
    }

    public function testGetCommissions_NonEuCardUsdExchangeRateZero(): void
    {
        $stubBin = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubBin->method('isBinFromEu')->willReturn(false);
        $stubRate = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubRate->method('getCurrencyExchangeRate')->willReturn(0.0);
        $tpc = new TransactionProcessController($stubBin, $stubRate);
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setCurrency('USD')
            ->setBin('bin');
        $this->assertSame((float)2, $tpc->getCommissions($transaction));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPrintCommissions_ExceptionNoInputFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stub = $this
            ->getMockBuilder(TransactionProcessController::class)
            ->setMethodsExcept(['printCommissions', 'getCommissions'])
            ->disableOriginalConstructor()
            ->getMock();
        $stub->printCommissions(['app.php']);
    }
}
