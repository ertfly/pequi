<?php

namespace PequiPHP\Tools;

class Date
{
    public static function formatToTime($format, $strTime)
    {
        $dateTime = \DateTime::createFromFormat($format, $strTime);
        if (is_object($dateTime) && $dateTime->format($format) == $strTime) {
            return $dateTime->getTimestamp();
        } else {
            return false;
        }
    }

    public static function showFullDate($date)
    {
        $date = explode('-', date('d-m-Y-H:i', strtotime($date)));
        $date[1] = self::showMonth($date[1]);
        $date = "{$date[0]} de {$date[1]} de {$date[2]} - {$date[3]}";
        return $date;
    }

    public static function showMonth($month)
    {
        $months = array(
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        );

        if (!isset($months[$month])) {
            throw new \Exception('Ocorreu um erro ao descrever o mês.');
        }

        return $months[$month];
    }

    public static function showMonthLess($month, $lowercase = false)
    {
        $months = array(
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez'
        );

        if (!isset($months[$month])) {
            throw new \Exception('Ocorreu um erro ao descrever o mês.');
        }

        if ($lowercase) {
            return mb_convert_case($months[$month], MB_CASE_LOWER);
        }

        return $months[$month];
    }

    public static function showDayWeek($day, $fullname = false)
    {
        $daysWeek = array(
            0 => 'Domingo',
            1 => 'Segunda' . ($fullname ? '-feira' : ''),
            2 => 'Terça' . ($fullname ? '-feira' : ''),
            3 => 'Quarta' . ($fullname ? '-feira' : ''),
            4 => 'Quinta' . ($fullname ? '-feira' : ''),
            5 => 'Sexta' . ($fullname ? '-feira' : ''),
            6 => 'Sábado'
        );
        if (!isset($daysWeek[$day])) {
            throw new \Exception('Ocorreu um erro ao descrever o dia da semana pequeno.');
        }

        return $daysWeek[$day];
    }

    public static function showDayWeekLess($day)
    {
        $daysWeek = array(
            0 => 'Dom',
            1 => 'Seg',
            2 => 'Ter',
            3 => 'Qua',
            4 => 'Qui',
            5 => 'Sex',
            6 => 'Sáb'
        );
        if (!isset($daysWeek[$day])) {
            throw new \Exception('Ocorreu um erro ao descrever o dia da semana pequeno.');
        }

        return $daysWeek[$day];
    }

    /**
     * @param string $date
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public static function toUs($date, $format)
    {
        $time = self::formatToTime($format, $date);
        if (date($format, $time) != $date) {
            throw new \Exception('Data inválida');
        }
        return date('Y-m-d', $time);
    }
}
