<?php
namespace Monitor\Format;

use Monitor\Contract\Format\FormatInterface;

class Xml implements FormatInterface
{

    /**
     * Convert xml resources to array
     *
     * @param  string $data
     * @return array $array
     */
    public function convertToArray($data)
    {
        $xml = simplexml_load_string($data);
        if ( ! $xml) {
            throw new \Exception('Cant\'t parse xml');
        }
        $array = json_decode(json_encode((array) $xml), true);
        if ( ! is_array($array)) {
            $array = [];
        }
        return $array;
    }
}
