<?php
function multiexplode ($delimiters,$string) {

    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

/**
 * 获取当前日期
 * @return string
 */
function getCurDateString() {
    $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
    $strDate = $dateCurrent->format('Y-m-d');

    return $strDate;
}

/**
 * 获取昨日日期
 * @return string
 */
function getPrevDateString($strDate = null) {

    // 默认是今日
    $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));

    if (!empty($strDate)) {
        $dateCurrent = getDateFromString($strDate);
    }

    $dateCurrent->add(\DateInterval::createFromDateString('yesterday'));
    $strDate = $dateCurrent->format('Y-m-d');

    return $strDate;
}

/**
 * 获取明日日期
 * @return string
 */
function getNextDateString($strDate = null) {

    // 默认是今日
    $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));

    if (!empty($strDate)) {
        $dateCurrent = getDateFromString($strDate);
    }

    $dateCurrent->add(\DateInterval::createFromDateString('tomorrow'));
    $strDate = $dateCurrent->format('Y-m-d');

    return $strDate;
}

/**
 * 日期string转DateTime
 * @param $string
 * @return bool|DateTime
 */
function getDateFromString($string) {
    return DateTime::createFromFormat('Y-m-d', $string);
}