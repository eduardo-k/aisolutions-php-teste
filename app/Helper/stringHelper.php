<?php

namespace App\Helper;

class stringHelper 
{
    public static function contemMes(string $string): bool
    {
        $meses = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
            'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

        foreach ($meses as $mes) {
            if (strpos(strtolower($string), $mes)) {
                return true;
            }
        }
    
        return false;
    }
}