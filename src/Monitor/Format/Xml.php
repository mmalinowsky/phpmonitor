<?php
namespace Monitor\Format;

class Xml implements FormatInterface
{

    /**
     * Convert xml resources to array
     *
     * @param string $data
     * @return array $array
     */
    public function convertToArray($data)
    {
        $xml = @simplexml_load_string($data);
        $array = json_decode(json_encode((array) $xml), true);
        if (!is_array($array)) {
            $array = array();
        }
        return $array;
    }
}