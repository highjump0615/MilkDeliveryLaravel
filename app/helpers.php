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
    $strDate = getStringFromDate($dateCurrent);

    return $strDate;
}

/**
 * 获取昨日日期
 * @return string
 */
function getPrevDateString($strDate = null) {

    return getDateWithOffsetString(-1, $strDate);
}

/**
 * 获取明日日期
 * @return string
 */
function getNextDateString($strDate = null) {
    return getDateWithOffsetString(1, $strDate);
}

/**
 * 获取偏移计算后的日期
 * @param $offset
 * @param null $strDate
 * @return mixed|null
 */
function getDateWithOffsetString($offset, $strDate = null) {

    // 默认是今日
    $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
    if (!empty($strDate)) {
        $dateCurrent = getDateFromString($strDate);
    }

    $delta = new \DateInterval(sprintf('P%dD', abs($offset)));

    // 增加
    if ($offset > 0) {
        $dateCurrent->add($delta);
    }
    // 减少
    else {
        $dateCurrent->sub($delta);
    }

    $strDate = getStringFromDate($dateCurrent);

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

/**
 * DateTime转string
 * @param $date DateTime
 * @return mixed
 */
function getStringFromDate($date) {
    return $date->format('Y-m-d');
}

/**
 * 默认0
 * @param $value
 * @return int
 */
function getEmptyArrayValue() {
    $args = func_get_args();
    $aryValue = $args[0];

    if (empty($aryValue)) {
        return 0;
    }

    for ($i = 1; $i < func_num_args(); $i++) {
        if (isset($aryValue[$args[$i]])) {
            $aryValue = $aryValue[$args[$i]];
        }
        else {
            return 0;
        }
    }

    return $aryValue;
}

/**
 * 如果val是null,返回0
 * @param $val
 * @return int
 */
function getEmptyValue($val) {
    return $val ?: 0;
}