<?php

namespace Pequi\Libraries\FormValidation;

use Pequi\Libraries\FormValidation\Validations\AbstractValidation;
use Pequi\Tools\Strings;

class FormValidation
{
    const REQUIRED = 'RequiredValidation';
    const EMAIL = 'EmailValidation';
    const DATE = 'DateValidation';
    const NUMERIC = 'NumericValidation';
    const CPF = 'CPFValidation';
    const CNPJ = 'CNPJValidation';
    const DECIMAL = 'DecimalValidation';
    const STR_LEN_MIN = 'StrLenMinValidation';
    const STR_LEN_MAX = 'StrLenMaxValidation';

    private $validations;
    private $description;
    private $options;
    private $value;
    private $newValue;

    public function __construct($value = null, $description = null, array $validations = null, $options = null)
    {
        $this->value = $value;
        $this->description = $description;
        $this->validations = $validations;
        $this->options = $options;
        if ($description) {
            $this->execute();
        }
    }

    public function executeValidation(&$value, $description, array $validations, $options = null)
    {
        $this->value = &$value;
        $this->description = $description;
        $this->validations = $validations;
        $this->options = $options;
        $this->execute();
    }

    public function execute()
    {
        if (!isset($this->validations) || !is_array($this->validations)) {
            throw new \Exception('Os tipos de validação precisam ser definidos');
        }
        foreach ($this->validations as $validation) {
            if (!is_string($validation)) {
                throw new \Exception('Tipo de validação informada não existe, informe string usando constantes');
            }
            $validationClass = Strings::classToClass(self::class) . '\\Validations\\' . $validation;
            if (!class_exists($validationClass)) {
                throw new \Exception('Tipo de validação informada não existe, informe string usando constantes');
            }
            $newValidation = new $validationClass($this->value, $this->description, $this->options);
            if (!($newValidation instanceof AbstractValidation)) {
                throw new \Exception('Utilize a classe abstrata para criar um novo tipo de validação');
            }

            $newValidation->validate();

            $this->newValue = $newValidation->hasNewValue();
            if ($this->newValue) {
                $this->value = $newValidation->getValue();
            }
        }
    }

    public function hasNewValue()
    {
        return $this->newValue;
    }

    public function getValue()
    {
        return $this->value;
    }
}
