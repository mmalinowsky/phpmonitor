<?php
namespace Monitor\Utils;

class PercentageHelper
{
    /**
     * Calculate service percentage
     * @access public
     * @param array $serverData server history
     * @param array $service
     * @return double
     */
    public function getServicePercentage(array $serverData, array $service)
    {
        list($column1, $column2) = array_pad(explode(":", $service['dbcolumns']), 2, 1);
        if (is_numeric($column2)) {
            $serverData[$column2] = $column2;
        }
        $percent = $this->getPercentage($serverData[$column1], $serverData[$column2]);
        return $percent;
    }

    /**
     * Get percentage
     * @access private
     * @param $value1
     * @param $value2
     * @return double
     */
    private function getPercentage($value1, $value2)
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
}
