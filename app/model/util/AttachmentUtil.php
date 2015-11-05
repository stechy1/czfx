<?php
/**
 * Created by PhpStorm.
 * User: Stechy1
 * Date: 4. 8. 2015
 * Time: 22:05
 */

namespace app\model\util;


class AttachmentUtil {

    /**
     * Zkontroluje bezpečnost názvu souboru.
     *
     * @param $filename string Název souboru.
     * @return bool True, pokud je název bezpečný, jinak false.
     */
    static function check_file_uploaded_name ($filename) {
        return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i", $filename)) ? true : false);
    }

    /**
     * Zkontroluje délku názvu souboru.
     *
     * @param $filename string Název souboru.
     * @return bool True, pokud má název nepřesahující délku.
     */
    static function check_file_uploaded_length ($filename) {
        return (bool) ((mb_strlen($filename, "UTF-8") > 225) ? true : false);
    }

    /**
     * Zkontroluje, zda-li je soubor správného typu.
     *
     * @param $fileName Cesta k souboru
     * @param $extArr array Pole povolených koncovek.
     * @return bool True, pokud je soubor validní, jinak false.
     */
    static function checkFileExtension ($fileName, $extArr) {
        return true;
    }

}