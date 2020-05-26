<?php

spl_autoload_register();
(new App\Controller\TransactionProcessController())
    ->printCommissions($argv);