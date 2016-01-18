<?php

/**
 * Calculate service percentage
 * @access public
 * @param array $server
 * @param array $service
 * @return double
 */
function servicePercentage(array $server, array $service)
{
    list($column1, $column2) = array_pad(explode(":", $service['dbcolumns']), 2, 1);
    if (is_numeric($column2)) {
        $server[$column2] = $column2;
    }
        $percent = getPercentage($server[$column1], $server[$column2]);
        return $percent;
}

/**
 * Get percentage
 * @access public
 * @param $value1
 * @param $value2
 * @return double
 */
function getPercentage($value1, $value2)
{
    if ($value2 == 0) {
        return 0;
    }
    $percent = round(($value1 / $value2) * 100, 0);
    if ($percent > 100) {
        return 100;
    }
    return $percent;
}
