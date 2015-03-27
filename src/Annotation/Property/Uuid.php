<?php

/**
 * This file is part of the Obo framework for application domain logic.
 * Obo framework is based on voluntary contributions from different developers.
 *
 * @link https://github.com/obophp/obo
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace obo\Annotation\Property;

class Uuid extends \obo\Annotation\Base\Property {


    protected $registerUuidGenerator = null;

    /**
     * @return string
     */
    public static function name() {
        return "uuid";
    }

    /**
     * @return array
     */
    public static function parametersDefinition() {
        return array("numberOfParameters" => "?");
    }

    /**
     * @param array $values
     * @return void
     */
    public function process($values) {
        parent::process($values);
        $this->registerUuidGenerator = $values[0];
    }

    /**
     * @param array $arguments
     * @throws \obo\Exceptions\BadDataTypeException
     * @throws \obo\Exceptions\PropertyNotFoundException
     */
    public function generateUuid(array $arguments) {
        try {
            $uuidGenerator = \obo\Services::serviceWithName(\obo\obo::UUID_GENERATOR);
        } catch (\obo\Exceptions\ServicesException $e) {
            throw new \obo\Exceptions\BadAnnotationException("UUID generator is not registered, register it via obo::setUuidGenerator()", null, $e);
        }

        if (\is_null($arguments["entity"]->valueForPropertyWithName($this->propertyInformation->name))){
            $arguments["entity"]->setValueForPropertyWithName($uuidGenerator->generateUuid(), $this->propertyInformation->name);
        }
    }

    /**
     * @return void
     */
    public function registerEvents() {

        if (!$this->registerUuidGenerator) return;

        \obo\Services::serviceWithName(\obo\obo::EVENT_MANAGER)->registerEvent(new \obo\Services\Events\Event([
            "onClassWithName" => $this->entityInformation->className,
            "name" => "beforeInsert",
            "actionAnonymousFunction" => function($arguments) {
                $arguments["annotation"]->generateUuid($arguments);
            },
            "actionArguments" => ["propertyName" => $this->propertyInformation->name, "annotation" => $this],
        ]));
    }

}
