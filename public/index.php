<?php
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) {
        return false;
    }
    if (in_array($errno, [E_DEPRECATED, E_USER_DEPRECATED])) {
        return true;
    }
    return false;
});

use App\Kernel;


require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
