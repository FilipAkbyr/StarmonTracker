<?php //Ruzne uzitecne metody
function validateDate($date, $format = 'Y-m-d') //Kontroluje zda je string ve formátu YYYY-MM-DD
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}