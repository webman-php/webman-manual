<?php
require __DIR__ . '/../../server/vendor/autoload.php';
require __DIR__ . '/filters.php';

ini_set('display_errors', 'on');
error_reporting(E_ALL);

global $cache, $lang;

$lang = basename(__FILE__, '.php');

$need_translate = function($text) {
    return !hasJapanese($text);
};

require __DIR__ . '/translate.php';