<?php

namespace toubeelib\core\dto;

use JsonSerializable;
use Respect\Validation\Validatable;
use Respect\Validation\Validator;

abstract class DTO implements JsonSerializable
{
    protected ?Validatable $businessValidator = null;

    public function __get(string $name):mixed {
        return property_exists($this, $name) ? $this->$name : throw new \Exception(static::class . ": Property $name does not exist");
    }

    public function toJSON(): string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    public function setBusinessValidator(Validatable $validator): void {
        $this->businessValidator = $validator;
    }
    public function validate(): void {
        $this->businessValidator ? $this->businessValidator->assert($this): null;
    }

    public function jsonSerialize(): array {
        //On retourne un tableau associatif des propriétés de l'objet sauf la propriété businessValidator
        $properties = get_object_vars($this);
        unset($properties['businessValidator']);
        return $properties;
    }
}