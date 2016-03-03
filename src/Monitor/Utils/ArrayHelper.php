<?php
namespace Monitor\Utils;

class ArrayHelper
{
    
    /**
     * Fill array with concret value when can't find same key in $arrayTofill as in $struct array
     *
     * @param  array $struct
     * @param  array $arrayToFill
     * @param  int   $value
     * @return array $arrayMerged
     */
    public function fillWithDefaultValue(array $struct, array $arrayToFill, $value = 0)
    {
        $arrayDiff = array_diff($struct, $arrayToFill);
        $arrayDiffFilled = array_fill_keys($arrayDiff, $value);
        $arrayMerged = array_merge($arrayDiffFilled, $arrayToFill);
        return $arrayMerged;
    }
}
