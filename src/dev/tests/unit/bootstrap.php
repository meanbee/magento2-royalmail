<?php


require_once __DIR__ . '/../../../vendor/autoload.php';
if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', dirname(__DIR__) . '/tmp');
}

require_once __DIR__ . '/autoload.php';

require __DIR__ . '/../../../vendor/magento/magento2-base/app/functions.php';

\Magento\Framework\Phrase::setRenderer(new \Magento\Framework\Phrase\Renderer\Placeholder());

error_reporting(E_ALL);
ini_set('display_errors', 1);
