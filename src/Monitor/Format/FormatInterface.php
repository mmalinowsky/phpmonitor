<?php
namespace Monitor\Format;

interface FormatInterface
{
    public function convertToArray($undecodedData);
}
