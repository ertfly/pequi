<?php

namespace Pequi\Libraries\FormValidation\Validations;

use Pequi\Translate;

class StrLenMaxValidation extends AbstractValidation
{
    private $message;

    public function validate()
    {
        $this->message = Translate::get('validation', 'StrLenMaxValidation', 'O campo %s deve conter no máximo %s caracteres');
        if ($this->value == '') {
            return;
        }

        if (!isset($this->options['size_max'])) {
            throw new \Exception('Informe a quantidade máxima dos caracteres');
        }
        if (mb_strlen($this->value) > $this->options['size_max']) {
            throw new \Exception(sprintf($this->message, $this->description, $this->options['size_max']));
        }
    }
}
