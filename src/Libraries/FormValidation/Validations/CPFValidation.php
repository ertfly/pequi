<?php

namespace Pequi\Libraries\FormValidation\Validations;

use Pequi\Tools\Strings;
use Pequi\Translate;

class CPFValidation extends AbstractValidation
{
    private $message;

    public function validate()
    {
        $this->message = Translate::get('validation', 'CPFValidation', 'O campo %s esta inválido');
        if ($this->value != '' && !$this->cpf($this->value)) {
            throw new \Exception(sprintf($this->message, $this->description));
        }

        if (isset($this->options['clear_format']) && is_bool($this->options['clear_format']) && $this->options['clear_format']) {
            $this->value = Strings::onlyNumber($this->value);
        }
    }

    public function cpf($cpf)
    { // Verifiva se o número digitado contém todos os digitos
        $cpf = str_pad(Strings::onlyNumber($cpf), 11, '0', STR_PAD_LEFT);

        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return false;
        } else {   // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf[$c] != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}
