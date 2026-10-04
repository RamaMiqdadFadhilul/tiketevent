<?php

require_once __DIR__ . '/config.php';

spl_autoload_register(function (string $class) {

    $file = __DIR__ . "/model/" . $class . ".php";

    if (file_exists($file)) {
        require_once $file;
    }

});
