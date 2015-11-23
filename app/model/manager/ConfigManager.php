<?php

namespace app\model\manager;


use app\model\service\exception\MyException;

class ConfigManager {

    private $configPHPfile;
    private $configJSONfile;
    /**
     * @var array
     */
    private $config;

    /**
     * Sestavi konfigurační řetězec
     *
     * @return string
     */
    private function buildConfig() {
        $string = "<?php\n";
        $string .= "include'hidden_config.php';\n";
        foreach($this->config as $key => $value) {
            $string .= 'define("' . $key . '", ';
            if ($value['type'] == "text")
                $string .= '"';
            $string .= $value['key'];
            if ($value['type'] == "text")
                $string .= '"';
            $string .= ');' . "\n";
        }

        return $string;
    }

    /**
     * Nastaví cestu ke konfiguračnímu souboru
     * Soubor nesmí obsahovat příponu
     *
     * @param $fileName
     */
    public function setConfigFile($fileName) {
        $this->configJSONfile = $fileName . '.json';
        $this->configPHPfile = $fileName . '.php';
    }

    /**
     * Načte aktuální konfiguraci
     */
    public function loadConfig() {
        $this->config = json_decode(file_get_contents($this->configJSONfile), true);
    }

    /**
     * Vrátí aktuální konfiguraci
     *
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Upraví hodnotu v nastavení
     *
     * @param $key string Klíč
     * @param $value string Hodnota
     * @throws MyException Pokud klíč neodpovídá zádnému klíči v poli
     */
    public function updateConfig($key, $value) {
        if (!array_key_exists($key, $this->config))
            throw new MyException("Zadaný klíč nebyl nalezen");

        $this->config[$key]['key'] = $value;
    }

    /**
     * Uloží změny konfigurace
     */
    public function saveConfig() {
        file_put_contents($this->configPHPfile, $this->buildConfig());
    }

    /**
     * Nastaví aktuální konfiguraci jako výchozí
     *
     * @param $newConfig array Nová konfigurace
     * @throws MyException
     */
    public function saveDefaultConfig($newConfig) {
        $tmpConfig = array();
        foreach ($newConfig as $key => $value) {
            if (!array_key_exists($key, $this->config))
                throw new MyException("Byl nalezen neznámý klíč");

            $tmpConfig[$key] = array();
            $tmpConfig[$key]['name'] = $this->config[$key]['name'];
            $tmpConfig[$key]['key'] = $value;
            $tmpConfig[$key]['type'] = $this->config[$key]['type'];
        }

        $this->config = $tmpConfig;

        file_put_contents($this->configJSONfile, json_encode($this->config));
        $this->saveConfig();
    }
}