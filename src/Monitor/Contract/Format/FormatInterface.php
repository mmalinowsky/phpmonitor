<?php
namespace Monitor\Contract\Format;

interface FormatInterface
{
    public function convertToArray($undecodedData);
}
