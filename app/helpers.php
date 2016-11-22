<?php
function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

/**
 * 获取当前
 * @return string
 */
function getCurDataString() {
    $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
    $strDate = $dateCurrent->format('Y-m-d');

    return $strDate;
}