<?php

use App\Exception\InvalidArgumentException;
use App\Exception\RemoteServiceException;
use App\Repository\BinProvider;
use PHPUnit\Framework\TestCase;

final class BinProviderTest extends TestCase
{
    public function testIsBinFromEu_Valid(): void
    {
        $bin = 'bin';
        $stub = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['isBinFromEu'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('getBinCountryFromService');
        $stub->method('getRemoteFileContents')->willReturn(
            $this->onConsecutiveCalls(
                '{"country":{"alpha2":"AT"}}',
                '{"country":{"alpha2":"BE"}}',
                '{"country":{"alpha2":"BG"}}',
                '{"country":{"alpha2":"CY"}}',
                '{"country":{"alpha2":"CZ"}}',
                '{"country":{"alpha2":"DE"}}',
                '{"country":{"alpha2":"DK"}}',
                '{"country":{"alpha2":"EE"}}',
                '{"country":{"alpha2":"ES"}}',
                '{"country":{"alpha2":"FI"}}',
                '{"country":{"alpha2":"FR"}}',
                '{"country":{"alpha2":"GR"}}',
                '{"country":{"alpha2":"HR"}}',
                '{"country":{"alpha2":"HU"}}',
                '{"country":{"alpha2":"IE"}}',
                '{"country":{"alpha2":"IT"}}',
                '{"country":{"alpha2":"LT"}}',
                '{"country":{"alpha2":"LU"}}',
                '{"country":{"alpha2":"LV"}}',
                '{"country":{"alpha2":"MT"}}',
                '{"country":{"alpha2":"NL"}}',
                '{"country":{"alpha2":"PO"}}',
                '{"country":{"alpha2":"PT"}}',
                '{"country":{"alpha2":"RO"}}',
                '{"country":{"alpha2":"SE"}}',
                '{"country":{"alpha2":"SI"}}',
                '{"country":{"alpha2":"SK"}}',
                '{"country":{"alpha2":"RU"}}',
                '{"country":{"alpha2":"UK"}}'
            )
        );
        $this->assertTrue($stub->isBinFromEu($bin)); // AT
        $this->assertTrue($stub->isBinFromEu($bin)); // BE
        $this->assertTrue($stub->isBinFromEu($bin)); // BG
        $this->assertTrue($stub->isBinFromEu($bin)); // CY
        $this->assertTrue($stub->isBinFromEu($bin)); // CZ
        $this->assertTrue($stub->isBinFromEu($bin)); // DE
        $this->assertTrue($stub->isBinFromEu($bin)); // DK
        $this->assertTrue($stub->isBinFromEu($bin)); // EE
        $this->assertTrue($stub->isBinFromEu($bin)); // ES
        $this->assertTrue($stub->isBinFromEu($bin)); // FI
        $this->assertTrue($stub->isBinFromEu($bin)); // FR
        $this->assertTrue($stub->isBinFromEu($bin)); // GR
        $this->assertTrue($stub->isBinFromEu($bin)); // HR
        $this->assertTrue($stub->isBinFromEu($bin)); // HU
        $this->assertTrue($stub->isBinFromEu($bin)); // IE
        $this->assertTrue($stub->isBinFromEu($bin)); // IT
        $this->assertTrue($stub->isBinFromEu($bin)); // LT
        $this->assertTrue($stub->isBinFromEu($bin)); // LU
        $this->assertTrue($stub->isBinFromEu($bin)); // LV
        $this->assertTrue($stub->isBinFromEu($bin)); // MT
        $this->assertTrue($stub->isBinFromEu($bin)); // NL
        $this->assertTrue($stub->isBinFromEu($bin)); // PO
        $this->assertTrue($stub->isBinFromEu($bin)); // PT
        $this->assertTrue($stub->isBinFromEu($bin)); // RO
        $this->assertTrue($stub->isBinFromEu($bin)); // SE
        $this->assertTrue($stub->isBinFromEu($bin)); // SI
        $this->assertTrue($stub->isBinFromEu($bin)); // SK
        $this->assertFalse($stub->isBinFromEu($bin)); // RU
        $this->assertFalse($stub->isBinFromEu($bin)); // UK
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testIsBinFromEu_InvalidJson1(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['isBinFromEu'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('getBinCountryFromService');
        $stub->method('getRemoteFileContents')->willReturn('{"alpha2":"AT"}');
        $stub->isBinFromEu('bin');
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testIsBinFromEu_InvalidJson2(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['isBinFromEu'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('getBinCountryFromService');
        $stub->method('getRemoteFileContents')->willReturn('{"country":"AT"}');
        $stub->isBinFromEu('bin');
    }

    /**
     * @expectedException RemoteServiceException
     */
    public function testIsBinFromEu_NoServiceData(): void
    {
        $this->expectException(RemoteServiceException::class);
        $stub = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['isBinFromEu'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('getBinCountryFromService');
        $stub->method('getRemoteFileContents')->willReturn(false);
        $stub->isBinFromEu('bin');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIsBinFromEu_NonExistingMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stub = $this
            ->getMockBuilder(BinProvider::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['isBinFromEu'])
            ->getMock();
        $stub->method('getProvidingMethod')->willReturn('invalidMethod');
        $stub->method('getRemoteFileContents')->willReturn(false);
        $stub->isBinFromEu('bin');
    }
}
