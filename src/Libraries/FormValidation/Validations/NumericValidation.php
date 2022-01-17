<?php

namespace PequiPHP\Libraries\FormValidation\Validations;

use PequiPHP\Translate;

class NumericValidation extends AbstractValidation
{
    private $message;

    public function validate()
    {
        $this->message = Translate::get('validation', 'NumericValidation', 'O campo %s deve conter apenas número');
        if (trim($this->value) != '' && !is_numeric($this->value)) {
            throw new \Exception(sprintf($this->message, $this->description));
        }
        return;
    }
}
