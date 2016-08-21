<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\factory\UserFactory;
use app\model\service\exception\MyException;
use app\model\util\SimpleImage;
use app\model\util\StringUtils;
use PDOException;

/**
 * Class UserManager - Správce jednotlivých uživatelů
 * @Inject Database
 * @Inject UserFactory
 * @Inject FileManager
 * @package app\model\manager
 */
class UserManager {

    const
        AVATAR_SIZE = 140,
        REMEMBER_COOKIE = "auth_token";

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
     * Získá sůl vybraného uživatele
     *
     * @param $userID int ID uživatele
     * @return mixed|null
     */
    private function getSalt ($userID) {
        return $this->database->queryItself("SELECT user_salt FROM users WHERE user_id = ?", [$userID]);
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
        $salt = StringUtils::randomString(32);
        $pass = StringUtils::createHash($data['password'], $salt);
        $hash = StringUtils::randomString(10);
        $checkCode = str_shuffle(StringUtils::createHash($pass));
        $time = time();
        $user = array(
            'user_nick' => $data['username'],
            'user_hash' => $hash,
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
     * Přihlásí uživatele do systému
     *
     * @param $data array Přihlašovací údaje
     *         email         E-mail
     *         password      Heslo
     *         remember-me   True, pokud se má přihlášení zapamatovat na delší dobu, jinak false
     * @throws MyException Pokud se přihlášení nezdaří
     * @internal param bool $rememberMe True, pokud se má přihlášení zapamatovat
     */
    public function login ($data) {
        $email = $data['email'];
        $password = $data['password'];
        $rememberMe = isset($data['remember-me']);

//        Získání údajů
        $fromDb = $this->database->queryOne('
                        SELECT user_id, user_salt AS selector
                        FROM users
                        LEFT JOIN auth_tokens ON auth_tokens_id = user_id
                        WHERE user_mail = ? AND user_role >= ?
                ', [$email, USER_ROLE_MEMBER]);
        if (!$fromDb)
            throw new MyException('Špatné jméno nebo heslo.');

//        Ověření hesla
        $salt = $fromDb['user_salt'];
        $userID = $fromDb['user_id'];
        $password = StringUtils::createHash($password, $salt);
        $fromDb = $this->database->queryOne(
            "SELECT user_id, auth_tokens_selector
            FROM users
            LEFT JOIN auth_tokens ON auth_tokens_id = user_id
            WHERE user_id = ? AND user_password = ? AND user_role >= ?
        ", [$userID, $password, USER_ROLE_MEMBER]);

//        Aktualizace stavu uživatele
        $this->database->update('users', ['user_online' => 1, 'user_last_login' => time()], 'WHERE user_id = ?', [$userID]);
        $_SESSION['user']['id'] = $userID;

        if ($rememberMe === true) {
            $this->setRememberCookie($userID, $fromDb['selector']);
        }
    }

    /**
     * Pokusí se přihlásit uživatele podle cookie
     *
     * @return bool True, pokud je přihlášení úspěšné, jinak false
     */
    public function loginFromCookie() {
        if (!empty($_SESSION['user']['id']) || empty($_COOKIE[self::REMEMBER_COOKIE]))
            return false;

        list($selector, $authenticator) = explode(':', $_COOKIE[self::REMEMBER_COOKIE]);
        $query = "SELECT
                    auth_tokens_user_id AS user_id,
                    auth_tokens_token AS token
                  FROM auth_tokens
                  WHERE auth_tokens_selector = ?";

        $fromDb = $this->database->queryOne($query, [$selector]);
        if (!$fromDb)
            return false;

        $token = $fromDb['token'];
        if (!hash_equals($token, hash('sha256', base64_decode($authenticator))))
            return false;

        $userId = $fromDb['user_id'];
        $_SESSION['user']['id'] = $userId;
        $this->database->update('users', ['user_online' => 1, 'user_last_login' => time()], 'WHERE user_id = ?', [$userId]);
        $this->setRememberCookie($userId, $selector);

        return true;
    }

    /**
     * Vytvoří cookie, pro zapamatování příhlášení
     *
     * @param $userID int
     * @param string|null $selector
     */
    private function setRememberCookie($userID, $selector = null) {
        $selectorIsEmpty = $selector == null;
        $selector = (!$selectorIsEmpty) ? $selector : base64_encode(openssl_random_pseudo_bytes(9));
        $authenticator = openssl_random_pseudo_bytes(33);
        $time = time() + 864000;
        setcookie(self::REMEMBER_COOKIE, $selector . ':' . base64_encode($authenticator), $time, '/');

        $data = [
            'auth_tokens_token' => hash('sha256', $authenticator),
            'auth_tokens_user_id' => $userID,
            'auth_tokens_expires' => $time
        ];

        if ($selectorIsEmpty) {
            $data['auth_tokens_selector'] = $selector;
            $this->database->insert('auth_tokens', $data);
        } else {
            $this->database->update('auth_tokens', $data, "WHERE auth_tokens_selector = ?", [$selector]);
        }
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

        $userID = $_SESSION['user']['id'];

        $oldSalt = $this->getSalt($userID);
        $oldHash = StringUtils::createHash($oldPassword, $oldSalt);

        $newSalt = StringUtils::randomString(32);
        $hash = StringUtils::createHash($newPassword, $newSalt);

        $fromDb = $this->database->update('users', ['user_password' => $hash, 'user_salt' => $newSalt], 'WHERE user_id = ? AND user_password = ?', [$userID, $oldHash]);

        if (!$fromDb)
            throw new MyException("Heslo se nepodařilo změnit");

        $this->database->delete('auth_tokens', "WHERE auth_tokens_user_id = ?", [$_SESSION['user']['id']]);

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
        $userID = $_SESSION['user']['id'];
        $salt = $this->getSalt($userID);
        $hash = StringUtils::createHash($password, $salt);
        $arr = array();

        foreach ($data as $key => $value)
            if (in_array($key, $keyArray))
                $arr['user_' . $key] = $value;

        $fromDb = $this->database->update('users', $arr, 'WHERE user_id = ? AND user_password = ?', [$userID, $hash]);

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
        unset($_COOKIE[self::REMEMBER_COOKIE]);
        setcookie(self::REMEMBER_COOKIE, '', -1, '/');
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
        $userID = $_SESSION['user']['id'];
        $salt = $this->getSalt($userID);
        $pass = StringUtils::createHash($password, $salt);
        $emptyUser = array(
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

        $fromDb = $this->database->update("users", $emptyUser, "WHERE user_id = ? AND user_password = ?", [$userID, $pass]);

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