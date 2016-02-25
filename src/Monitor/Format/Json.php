<?php
namespace Monitor\Format;

use Monitor\Contract\Format\FormatInterface;

class Json implements FormatInterface
{

    /**
     * Convert json resources to array
     *
     * @param  string $data
     * @return array $array
     */
    public function convertToArray($data)
    {
        $array = json_decode($data, true);
        if ( ! is_array($array)) {
            $array = [];
        }
        return $array;
    }
}
