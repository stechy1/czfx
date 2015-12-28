<?php

namespace app\model\manager;


use app\model\service\exception\MyException;

/**
 * Class FileManager - Správce souborového systému
 * @package app\model\manager
 */
class FileManager {

    const
        FOLDER_ATTACHMENT = "attachments/",
        FOLDER_UPLOADS = "uploads",
        FOLDER_CATEGORY = "category",
        FOLDER_IMAGE = "image",
        FOLDER_TMP = "tmp",
        FOLDER_AVATAR = "avatar",
        FOLDER_FORUM_IMAGE = "forumImage";

    private $folderRoot;
    private $folders;

    function __construct() {
        $this->init();
    }

    /**
     * Inicializace instance
     */
    private function init() {
        $this->folderRoot = $_SERVER['DOCUMENT_ROOT'] . '/';

        $this->folders[self::FOLDER_UPLOADS] = $this->folderRoot . "uploads/";
        $this->folders[self::FOLDER_CATEGORY] = $this->folders['uploads'] . "category/";
        $this->folders[self::FOLDER_IMAGE] = $this->folders['uploads'] . "image/";
        $this->folders[self::FOLDER_TMP] = $this->folders['uploads'] . "tmp/";
        $this->folders[self::FOLDER_AVATAR] = $this->folders['image'] . "avatar/";
        $this->folders[self::FOLDER_FORUM_IMAGE] = $this->folders['image'] . "/forum";

        foreach ($this->folders as $folder)
            $this->createDirectory($folder);
    }

    /**
     * Vytvoří složku attachments v adresáři s článkem
     *
     * @param $artFolder string složka s článkem
     * @return string Cestu ke složce attachment pro zadaný článek
     */
    public static function getAttachmentsFolder($artFolder) {
        $path = $artFolder . "/" . self::FOLDER_ATTACHMENT;
        if (!file_exists($path))
            mkdir($path);

        return $path;
    }

    /**
     * Metoda rekurzivně projede zadanou cestu a smaže všechno, co jí příjde do cesty
     *
     * @param $str string Cesta k souboru/složce
     * @return bool False, pokud není co smazat, jinak true
     */
    public static function recursiveDelete($str) {
        if (is_file($str)) {
            return @unlink($str);
        }
        elseif (is_dir($str)) {
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path) {
                self::recursiveDelete($path);
            }
            return @rmdir($str);
        }

        return false;
    }

    /**
     * Metoda pro přesun souborů z jedné složky do druhé. Funguje rekurzivně
     *
     * @param $sourceDir string Zdrojová složky - odkud se mají soubory přesunout
     * @param $destDir string cílová složka - kam se mají soubory přesunout
     * @throws MyException Pokud zdroj nebo cíl není složka
     */
    public static function moveFiles($sourceDir, $destDir) {

        if (!is_dir($sourceDir))
            throw new MyException("Zdroj není složka");
        if (!is_dir($destDir))
            throw new MyException("Cíl není složka");
        $fileArray = array_diff(scandir($sourceDir), array('..', '.'));
        foreach ($fileArray as $tmpFile) {
            if (is_dir($tmpFile))
                self::moveFiles($tmpFile, $destDir . "/" . $tmpFile);
            else
                rename($sourceDir . "/" . $tmpFile, $destDir . $tmpFile);
        }
    }

    /**
     * Metoda pro přesun souborů z dočasné složky serveru do cílové
     *
     * @param $fileName string Název souboru, který se má přesunout
     * @param $destDir string cílová složka - kam se mají soubory přesunout
     * @throws MyException Pokud se přesun nepodaří
     */
    public static function moveUploadedFiles($fileName, $destDir) {
        $success = move_uploaded_file($fileName, $destDir);

        if (!$success)
            throw new MyException("Nepodařilo se přesunout soubor");
    }

    /**
     * Metoda pro získání obsahu z adresáře
     *
     * @param $dir string Cesta k adresáři
     * @return array Pole souborů
     */
    public static function getFilesFromDirectory($dir) {
        return array_diff(scandir($dir), array('..', '.'));
    }

    /**
     * Pomocná metoda pro vytvoření složky, pokud neexistuje
     *
     * @param $path string Cesta ke složce
     */
    private function createDirectory($path) {
        if (!file_exists($path))
            mkdir($path, 0777, true);
    }

    /**
     * Metoda pro získání cesty k zadané složce
     *
     * @param $name string Název složky
     * @return string Cestu k zadané složce
     * @throws MyException Pokud požadovaná složka neexistuje
     */
    public function getDirectory($name) {
        if (!array_key_exists($name, $this->folders))
            throw new MyException('Požadovaná složka neexistuje');

        return $this->folders[$name];
    }

    /**
     * Vytvoří novou složku pro článek
     *
     * @param $categoryURL string URL adresa kategorie článku
     * @param $articleURL string URL adresa článku
     * @return string Cestu k složce s článkem
     */
    public function createArticleDirectory($categoryURL, $articleURL) {
        $path = $this->folders[self::FOLDER_CATEGORY] . $categoryURL . "/" . $articleURL;
        $this->createDirectory($path);

        return $path;
    }

    /**
     * Přečte soubor a vrátí jeho obsah v textové podobě
     *
     * @param $categoryURL string URL adresa kategorie článku (složka kategorie, ve které se článek nachází)
     * @param $articleURL string URL adresa článku (složka článku, ve které se článek nachází)
     * @return string Obsah souboru
     * @throws MyException Pokud článek není nalezen
     */
    public function getArticleContent($categoryURL, $articleURL) {
        $path = $this->folders[self::FOLDER_CATEGORY] . $categoryURL . "/" . $articleURL . "/" . $articleURL . '.markdown';
        if (!file_exists($path))
            throw new MyException("Požadovaný soubor nebyl nalezen");

        return $this->readFile($path);
    }

    /**
     * Vytvoří nový soubor a zapíše do něj obsah. Pokud soubor existuje, obsah se přepíše
     *
     * @param $path string Cesta k souboru
     * @param $text string Obsah souboru
     */
    public function writeFile($path, $text) {
        $file = fopen($path, "w");
        fwrite($file, $text);
        fclose($file);
    }

    /**
     * Přečte soubor a vrátí obsah
     *
     * @param $path string Cesta k souboru
     * @return string Obsah souboru
     */
    public function readFile($path) {
        return file_get_contents($path);
    }

    /**
     * Vytvoří dočasnou složku pro uživatele
     *
     * Pokud složka již existuje, tak smaže její obsah
     */
    public function createTmpDirectory() {
        $tmpDirectory = $this->getTmpDirectory();
        if (file_exists($tmpDirectory))
            $this->clearTmpDirectory($tmpDirectory);
        else
            $this->createDirectory($tmpDirectory);
    }

    /**
     * @return string Vrátí cestu k dočasné složce uživatele.
     */
    public function getTmpDirectory() {
        $dir = $this->folders[self::FOLDER_TMP] . $_SESSION['user']['id'] . "/";
        if (!file_exists($dir))
            $this->createDirectory($dir);

        return $dir;
    }

    /**
     * Metoda vyčistí junk-files z dočasné složky uživatele
     *
     * @param $tmpDirectory string Cesta k dočasné složce uživatele
     */
    public function clearTmpDirectory($tmpDirectory = null) {
        $tmpDirectory = $tmpDirectory | $this->getTmpDirectory();
        $this->recursiveDelete($tmpDirectory);
    }

    /**
     * Upraví cestu ze statické na relativní
     *
     * @param $staticPath string Statická cesta
     * @return string Relativná cestu
     */
    public function getRelativePath($staticPath) {
        return str_replace($this->folderRoot, "/", $staticPath);
    }
}