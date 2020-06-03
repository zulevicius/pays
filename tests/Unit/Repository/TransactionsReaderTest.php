<?php

use App\Entity\Transaction;
use App\Repository\TransactionsReader;
use PHPUnit\Framework\TestCase;

final class TransactionsReaderTest extends TestCase
{
    public function testReadRecords(): void
    {
        $stub = $this
            ->getMockBuilder(TransactionsReader::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['readRecords'])
            ->getMock();
        $stub->method('getFileByLines')->willReturn([
            '{"bin":"41417360","amount":"130.00","currency":"USD"}',
            '{"bin":"4745030","amount":"2000.00","currency":"GBP"}'
        ]);
        $this->assertEquals(
            [
                (new Transaction())
                    ->setBin('41417360')
                    ->setAmount(130)
                    ->setCurrency('USD'),
                (new Transaction())
                    ->setBin('4745030')
                    ->setAmount(2000)
                    ->setCurrency('GBP'),
            ],
            $stub->readRecords('filename'));
    }
}
