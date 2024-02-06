<?php


function hasChinese($content) {
    return preg_match("/[\x{4e00}-\x{9fa5}]+/u", $content);
}

function hasChineseTraditional($text) {
    return @iconv('UTF-8', 'GB2312', $text) === false;
}

function hasJapanese($text) {
    return  preg_match('/[\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $text);
}

function hasKorean($string) {
    return preg_match('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', $string);
}
