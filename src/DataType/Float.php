<?php

/**
 * This file is part of the Obo framework for application domain logic.
 * Obo framework is based on voluntary contributions from different developers.
 *
 * @link https://github.com/obophp/obo
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace obo\DataType;

class Float extends \obo\DataType\Base\DataType {

    /**
     * @return string
     */
    public function name() {
        return "float";
    }

    /**
     * @param mixed $value
     * @throws \obo\Exceptions\BadDataTypeException
     * @return void
     */
    public function validate($value) {
        parent::validate($value);
        if (!\is_null($value) && (!\is_numeric($value) || !\is_float($value * 1))) throw new \obo\Exceptions\BadDataTypeException("Value for property with name '{$this->propertyInformation->name}' must be of float data type. Given value couldn't be converted.");
    }

    /**
     * @param mixed $value
     * @return float
     */
    public function convertValue($value) {
        return (float) $value;
    }

    /**
     * @return void
     */
    public function registerEvents() {
        \obo\Services::serviceWithName(\obo\obo::EVENT_MANAGER)->registerEvent(new \obo\Services\Events\Event(array(
            "onClassWithName" => $this->propertyInformation->entityInformation->className,
            "name" => "beforeWrite" . \ucfirst($this->propertyInformation->name),
            "actionAnonymousFunction" => function($arguments) {
                $arguments["dataType"]->validate($arguments["propertyValue"]["new"]);
                $arguments["propertyValue"]["new"] = $arguments["dataType"]->convertValue($arguments["propertyValue"]["new"]);
            },
            "actionArguments" => array("dataType" => $this),
        )));
    }
}
