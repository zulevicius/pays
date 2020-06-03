<?php

spl_autoload_register();
(new App\Controller\TransactionProcessController(
    (new App\Repository\BinProvider()),
    (new App\Repository\ExchangeRatesProvider())
))
    ->printCommissions($argv);