<?php

namespace Pequi\Libraries\FormValidation\Validations;

use Pequi\Translate;

class RequiredValidation extends AbstractValidation
{
    private $message;

    public function validate()
    {
        $this->message = Translate::get('validation', 'RequiredValidation', 'O campo %s é obrigatório');
        if ($this->value == '') {
            throw new \Exception(sprintf($this->message, $this->description));
        }
    }
}
