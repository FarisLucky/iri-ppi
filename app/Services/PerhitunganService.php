<?php

namespace App\Services;

class PerhitunganService
{

    public static function hitung($ttlPasien, $lmInfus)
    {
        return ($ttlPasien / $lmInfus);
    }

    public static function plebitis($ttlPasien, $lmInfus)
    {
        return self::hitung($ttlPasien, $lmInfus) * 1000;
    }

    public static function isk($ttlPasien, $lmInfus)
    {
        return self::hitung($ttlPasien, $lmInfus) * 1000;
    }

    public static function ido($ttlPasien, $lmInfus)
    {
        return self::hitung($ttlPasien, $lmInfus) * 100;
    }
}
