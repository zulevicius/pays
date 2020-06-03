<?php

use App\Exception\InvalidArgumentException;
use App\Exception\RemoteServiceException;
use App\Repository\ExchangeRatesProvider;
use PHPUnit\Framework\TestCase;

final class ExchangeRatesProviderTest extends TestCase
{
    public function testGetCurrencyExchangeRate_Valid(): void
    {
        $stub = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCurrencyExchangeRate'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('getCurrencyEurRateFromService');
        $stub->method('getRemoteFileContents')->willReturn(
            $this->onConsecutiveCalls(
                '{"rates":{"PLN":4.4135}}',
                false
            )
        );
        $this->assertSame(4.4135, $stub->getCurrencyExchangeRate('PLN'));
        $this->assertSame((float)1, $stub->getCurrencyExchangeRate('EUR'));
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testGetCurrencyExchangeRate_InvalidJson1(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCurrencyExchangeRate'])
            ->getMock();

        $stub->method('getProvidingMethod')->willReturn('getCurrencyEurRateFromService');
        $stub->method('getRemoteFileContents')->willReturn('{"rates":{}}');
        $stub->getCurrencyExchangeRate('PLN');
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testGetCurrencyExchangeRate_InvalidJson2(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCurrencyExchangeRate'])
            ->getMock();

        $stub->method('getProvidingMethod')->willReturn('getCurrencyEurRateFromService');
        $stub->method('getRemoteFileContents')->willReturn('{"PLN":4.4135}');
        $stub->getCurrencyExchangeRate('PLN');
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testGetCurrencyExchangeRate_NoServiceData(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCurrencyExchangeRate'])
            ->getMock();

        $stub->method('getProvidingMethod')->willReturn('getCurrencyEurRateFromService');
        $stub->method('getRemoteFileContents')->willReturn(false);
        $stub->getCurrencyExchangeRate('PLN');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetCurrencyExchangeRate_NonExistingMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stub = $this
            ->getMockBuilder(ExchangeRatesProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['getCurrencyExchangeRate'])
            ->getMock();

        $stub->method('getProvidingMethod')->willReturn('invalidMethod');
        $stub->method('getRemoteFileContents')->willReturn(false);
        $stub->getCurrencyExchangeRate('PLN');
    }
}
