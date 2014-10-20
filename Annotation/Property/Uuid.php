<?php

/**
 * This file is part of framework Obo Development version (http://www.obophp.org/)
 * @link http://www.obophp.org/
 * @author Roman Pavlík
 * @copyright (c) 2011 - 2014 Adam Suba
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace obo\Annotation\Property;

class Uuid extends \obo\Annotation\Base\Property {

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
     * @param array $arguments
     * @throws \obo\Exceptions\BadDataTypeException
     * @throws \obo\Exceptions\PropertyNotFoundException
     */
    public function createUuid(array $arguments) {
        try {
            $uuidGenerator = \obo\Services::serviceWithName(\obo\obo::UUID_GENERATOR);
        } catch (\obo\Exceptions\ServicesException $e) {
            throw new \obo\Exceptions\BadAnnotationException("UUID generator is not registered, registr it via obo::setUuidGenerator()", null, $e);
        }

        $arguments["entity"]->setValueForPropertyWithName($uuidGenerator->createUuid(), $this->propertyInformation->name);
    }

    public function registerEvents() {
        \obo\Services::serviceWithName(\obo\obo::EVENT_MANAGER)->registerEvent(new \obo\Services\Events\Event([
            "onClassWithName" => $this->entityInformation->className,
            "name" => "beforeInsert",
            "actionAnonymousFunction" => function($arguments) {
                $arguments["annotation"]->createUuid($arguments);
            },
            "actionArguments" => ["propertyName" => $this->propertyInformation->name, "annotation" => $this],
        ]));
    }
}
