<?php

namespace app\model\util;

use DateTime;
use InvalidArgumentException;

/**
 * Class DateUtils
 * Jednoduchá knihovní třída, pro prácí s datem a časem.
 * @package model\util
 */
class DateUtils {
    /**
     * Formát datum a čas
     */
    const DATETIME_FORMAT = 'j.n.Y G:i:s';
    /**
     * Formát datum
     */
    const DATE_FORMAT = 'j.n.Y';
    /**
     * Formát čas
     */
    const TIME_FORMAT = 'G:i:s';
    /**
     * Databázový formát datum a čas
     */
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
    /**
     * Databázový formát datum
     */
    const DB_DATE_FORMAT = 'Y-m-d';
    /**
     * Databázový formát čas
     */
    const DB_TIME_FORMAT = 'H:i:s';
    /**
     * @var array České názvy měsíců
     */
    private static $months = array('ledna', 'února', 'března', 'dubna', 'května', 'června', 'července', 'srpna', 'září', 'října', 'listopadu', 'prosince');
    /**
     * @var array Chybové hlášky
     */
    private static $errorMessages = array(self::DATE_FORMAT => 'Neplatné datum, zadejte ho prosím ve tvaru dd.mm.rrrr', self::TIME_FORMAT => 'Neplatný čas, zadejte ho prosím ve tvaru hh:mm, můžete dodat i vteřiny', self::DATETIME_FORMAT => 'Neplatné datum nebo čas, zadejte prosím hodnotu ve tvaru dd.mm.rrrr hh:mm, případně vteřiny',);
    /**
     * @var array Slovník pro převod mezi českým a databázovým formátem
     */
    private static $formatDictionary = array(self::DATE_FORMAT => self::DB_DATE_FORMAT, self::DATETIME_FORMAT => self::DB_DATETIME_FORMAT, self::TIME_FORMAT => self::DB_TIME_FORMAT,);

    /**
     * Naparsuje české datum a čas podle formátu
     *
     * @param string $date Datum a čas
     * @param string $format Formát
     * @return string Datum a čas v databázovém formátu
     * @throws InvalidArgumentException
     */
    public static function parseDateTime ($date, $format = self::DATETIME_FORMAT) {
        if (mb_substr_count($date, ':') == 1)
            $date .= ':00';
        // Smaže mezery před nebo za separátory
        $a = array('/([\.\:\/])\s+/', '/\s+([\.\:\/])/', '/\s{2,}/');
        $b = array('\1', '\1', ' ');
        $date = trim(preg_replace($a, $b, $date));
        // Smaže nuly před čísly
        $a = array('/^0(\d+)/', '/([\.\/])0(\d+)/');
        $b = array('\1', '\1\2');
        $date = preg_replace($a, $b, $date);
        // Vytvoří instanci DateTime, která zkontroluje zda zadané datum existuje
        $dateTime = DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();
        // Vyvolání chyby
        if ($errors['warning_count'] + $errors['error_count'] > 0) {
            if (in_array($format, self::$errorMessages))
                throw new InvalidArgumentException(self::$errorMessages[$format]); else
                throw new InvalidArgumentException('Neplatná hodnota');
        }
        // Návrat data v MySQL formátu
        return $dateTime->format(self::$formatDictionary[$format]);
    }

    /**
     * Zjistí, zda je dané datum a čas validní
     *
     * @param string $date Datum a čas
     * @param string $format Formát data a času
     * @return bool Zda je hodnota validní
     */
    public static function validDate ($date, $format = self::DATETIME_FORMAT) {
        try {
            self::parseDateTime($date, $format);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Zformátuje instanci DateTime na formát např. "Dnes"
     *
     * @param DateTime $dateTime Instance DateTime
     * @return string Zformátovaná hodnota
     */
    private static function getPrettyDate ($dateTime) {
        $now = new DateTime();
        if ($dateTime->format('Y') != $now->format('Y'))
            return $dateTime->format('j.n.Y');
        $dayMonth = $dateTime->format('d-m');
        if ($dayMonth == $now->format('d-m'))
            return "Dnes";
        $now->modify('-1 DAY');
        if ($dayMonth == $now->format('d-m'))
            return "Včera";
        $now->modify('+2 DAYS');
        if ($dayMonth == $now->format('d-m'))
            return "Zítra";
        return $dateTime->format('j.') . self::$months[$dateTime->format('n') - 1];
    }

    /**
     * Vytvoří instanci DateTime z daného vstupu. Podporuje UNIX timestamp
     *
     * @param string $date Řetězec s datem, případně časem
     * @return DateTime Instance DateTime
     */
    public static function getDateTime ($date) {
        if (ctype_digit($date))
            $date = '@' . $date;
        return new DateTime($date);
    }

    /**
     * Zformátuje datum z libovolné stringové podoby na tvar např. "Dnes"
     *
     * @param string $date Datum ke zformátování
     * @return string Zformátované datum
     */
    public static function prettyDate ($date) {
        if (!$date)
            return "unknown";
        return self::getPrettyDate(self::getDateTime($date));
    }

    /**
     * Zformátuje datum a čas z libovolné stringové podoby na tvar např. "Dnes 15:21"
     *
     * @param string $date Datum ke zformátování
     * @return string Zformátované datum
     */
    public static function prettyDateTime ($date) {
        $dateTime = self::getDateTime($date);
        return self::getPrettyDate($dateTime) . $dateTime->format(' H:i:s');
    }

    /**
     * Zformátuje datum z libovolné stringové podoby
     *
     * @param string $date Datum ke zformátování
     * @return string Zformátované datum
     */
    public static function formatDate ($date) {
        $dateTime = self::getDateTime($date);
        return $dateTime->format('d.m.Y');
    }

    /**
     * Zformátuje datum a čas z libovolné stringové podoby
     *
     * @param string $date Datum a čas ke zformátování
     * @return string Zformátované datum
     */
    public static function formatDateTime ($date) {
        $dateTime = self::getDateTime($date);
        return $dateTime->format('d.m.Y H:i:s');
    }

    /**
     * Vrací aktuální datum v DB podobě
     *
     * @return string Datum v DB podobě
     */
    public static function dbNow () {
        $dateTime = new DateTime();
        return $dateTime->format(self::DB_DATETIME_FORMAT);
    }

} 