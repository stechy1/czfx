<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\factory\UserFactory;
use app\model\service\exception\MyException;
use app\model\util\SimpleImage;
use PDOException;

/**
 * Class UserManager
 * @Inject Database
 * @Inject UserFactory
 * @Inject FileManager
 * @package app\model\manager
 */
class UserManager {

    const
        AVATAR_SIZE = 140;

    /**
     * @var Database
     */
    private $database;
    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var FileManager
     */
    private $filemanager;

    /**
     * Vrátí otisk hesla
     *
     * @param $heslo string Čisté heslo
     * @return string Hash osoleného hesla
     */
    public function hash ($heslo) {
        $sul = PASSWORD_SALT;
        return hash('sha512', $heslo . $sul);
    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param $data array Registrační údaje
     *         username     Uživatelské jméno
     *         password     Heslo
     *         password2    Heslo pro kontrolu
     *         email        E-mail
     * @throws MyException Pokud se registrace nezdaří
     */
    public function register ($data) {


        if ($data['password'] != $data['password2'])
            throw new MyException('Hesla nesouhlasí.');
        $pass = $this->hash($data['password']);
        $checkCode = str_shuffle($this->hash($pass));
        $time = time();
        $user = array(
            'user_nick' => $data['username'],
            'user_password' => $pass,
            'user_mail' => $data['email'],
            'user_first_login' => $time,
            'user_last_login' => $time,
            'user_activation_code' => $checkCode
        );
        try {
            $this->database->insert('users', $user);


        } catch (PDOException $chyba) {
            throw new MyException('Uživatel s touto e-mailovou adresou je již zaregistrovaný.');
        }
    }

    /**
     * Prihlásí uživatele do systému
     *
     * @param $data array Přihlašovací údaje
     *         email      E-mail
     *         password   Heslo
     * @throws MyException Pokud se přihlášení nezdaří
     */
    public function login ($data) {
        $email = $data['email'];
        $password = $data['password'];
        $fromDb = $this->database->queryOne('
                        SELECT user_id
                        FROM users
                        WHERE user_mail = ? AND user_password = ? AND user_role >= ?
                ', [$email, $this->hash($password), USER_ROLE_MEMBER]);
        if (!$fromDb)
            throw new MyException('Špatné jméno nebo heslo.');

        $this->database->update('users', ['user_online' => 1, 'user_last_login' => time()], 'WHERE user_id = ?', [$fromDb['user_id']]);
        $_SESSION['user']['id'] = $fromDb['user_id'];
    }

    /**
     * Změní uživatelské heslo
     *
     * @param $data
     *         oldPassword      Staré heslo
     *         newPassword      Nové heslo
     *         newPassword2     Nové heslo pro kontrolu
     * @return bool True, pokud byla změna hesla úspěšná
     * @throws MyException Pokud nejsou vyplněny všechny položky, nebo pokud se hesla neshodují, nebo pokud se nepodaří heslo změnit
     */
    public function changePassword ($data) {
        if (!isset($data['oldPassword']) || !isset($data['newPassword']) || !isset($data['newPassword2']))
            throw new MyException("Nejsou vyplněny všechny povinné položky");

        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];
        $newPassword2= $data['newPassword2'];

        if ($newPassword == $oldPassword)
            throw new MyException("Nové heslo se nesmí shodovat se starým heslem");

        if ($newPassword != $newPassword2)
            throw new MyException("Nové heslo se neshoduje s kontrolním");

        $oldHash = $this->hash($oldPassword);
        $hash = $this->hash($newPassword);

        $fromDb = $this->database->update('users', ['user_password' => $hash], 'WHERE user_id = ? AND user_password = ?', [$_SESSION['user']['id'], $oldHash]);

        if (!$fromDb)
            throw new MyException("Heslo se nepodařilo změnit");

        return true;
    }

    /**
     * Aktualizuje pouze ty prvky, které se nacházejí v poli keyArray
     *
     * @param $data array Pole nových dat
     * @param $keyArray array Pole klíčových prvků, které se mají aktualizovat
     * @param $password string Aktuální heslo pro kontrolu
     * @return bool True, pokud se povedlo údaje aktualizovat
     * @throws MyException Pokud se nepovedlo údaje aktualizovat
     */
    public function updateData ($data, $keyArray, $password) {
        $hash = $this->hash($password);
        $arr = array();

        foreach ($data as $key => $value)
            if (in_array($key, $keyArray))
                $arr['user_' . $key] = $value;

        $fromDb = $this->database->update('users', $arr, 'WHERE user_id = ? AND user_password = ?', [$_SESSION['user']['id'], $hash]);

        if (!$fromDb)
            throw new MyException("Údaje se nepodařilo změnit");

        return true;
    }

    /**
     * Změní obrázek uživateli na nový
     *
     * @param $avatar array Pole informací o obrázku
     * @return string Relativní cestu k obrázku
     * @throws MyException Pokud se změna obrázku nepovedla
     */
    public function changeAvatar ($avatar) {
        $user = $this->userfactory->getUserFromSession();
        $avatarDir = $this->filemanager->getDirectory(FileManager::FOLDER_AVATAR);
        $avatarName = "avatar_" . time();

        $img = new SimpleImage($avatar['tmp_name']);
        $img->square(self::AVATAR_SIZE);
        $img->save($avatarDir . $avatarName . ".png", IMAGETYPE_PNG);

        $fromDb = $this->database->update('users', ['user_avatar' => $avatarName], 'WHERE user_id = ?', [$user->getId()]);
        if (!$fromDb)
            throw new MyException("Změna obrázku se nepovedla");

        $this->filemanager->recursiveDelete($avatarDir . $user->getAvatar() . ".png");

        return $this->filemanager->getRelativePath($avatarDir . $avatarName . ".png");
    }

    /**
     * Odhlásí uživatele ze systému
     *
     * @param bool $changeDb Pokud je true, změní se údaj v databázi
     * @return bool True, pokud je odhlášení v pořádku
     * @throws MyException Pokud se odhlášení nezdařilo
     */
    public function logout ($changeDb = true) {
        $fromDb = null;
        if ($changeDb)
            $fromDb = $this->database->update('users', ['user_online' => 0], 'WHERE user_id = ?', [$_SESSION['user']['id']]);
        unset($_SESSION['user']);
        if (!$changeDb || $fromDb)
            return true;

        throw new MyException("Není koho odhlašovat");
    }

    /**
     * Smaže uživatele ze systému
     *
     * @param $password string Potvrzovací heslo
     * @return bool True, pokud se smazání povedlo, jinak false
     * @throws MyException Pokud se smazání nepodařilo
     */
    public function delete ($password) {
        $id = $_SESSION['user']['id'];
        $pass = $this->hash($password);
        $emptyUser = array(
            "user_nick" => "Bývalý člen",
            "user_password" => "",
            "user_role" => "1",
            "user_online" => "0",
            "user_banned" => "0",
            "user_activated" => "0",
            "user_activation_code" => "",
            "user_name" => "",
            "user_age" => "",
            "user_avatar" => "empty-image",
            "user_region" => "",
            "user_city" => "",
            "user_motto" => "",
            "user_skill" => "",

        );

        $fromDb = $this->database->update("users", $emptyUser, "WHERE user_id = ? AND user_password = ?", [$id, $pass]);

        if (!$fromDb)
            throw new MyException("Špatné heslo");

        return true;
    }

    /**
     * Vrátí true, pokud je uživatel přihlášen
     *
     * @return bool True, pokud je uživatel přihlášený, jinak false
     */
    public function isLoged () {
        return isset($_SESSION['user']);
    }

    /**
     * Vrátí true, pokud je účet aktivovaný, jinak false
     *
     * @return bool True, pokud je účet aktivovaný, jinak false
     * @throws MyException Pokud není uživatel přihlášen
     */
    public function isActivated () {
        if ($_SESSION['user']['user_activated'])
            return $_SESSION['user']['user_activated'];

        throw new MyException("Uživatel není prihlášený.");
    }

    /**
     * Ověří aktivační kód
     *
     * @param $code string Aktivační kód
     * @return bool True, pokud se aktivace zdařila
     * @throws MyException Pokud se aktivace nezdařila
     */
    public function checkCode ($code) {
        $fromDb = $this->database->update("users", ["user_activated" => 1], "WHERE user_activation_code = ?", [$code]);

        if ($fromDb) {
            if ($_SESSION['user'])
                $_SESSION['user']['activated'] = 1;
            return true;
        }
        throw new MyException("Kód nelze ověřit");
    }
}