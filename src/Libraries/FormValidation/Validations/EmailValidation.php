<?php

namespace Pequi\Libraries\FormValidation\Validations;

use Pequi\Translate;

class EmailValidation extends AbstractValidation
{
    private $message;

    public function validate()
    {
        $this->message = Translate::get('validation', 'EmailValidation', 'O %s esta inválido');

        if ($this->value != '' && !filter_var(trim($this->value), FILTER_VALIDATE_EMAIL)) {
            throw new \Exception(sprintf($this->message, $this->description));
        }
    }
}
