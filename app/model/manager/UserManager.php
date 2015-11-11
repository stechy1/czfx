<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\factory\UserFactory;
use app\model\service\exception\MyException;
use app\model\User;
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
        $sul = '~e_;*G=_;G4T%;n;4V*D#$%';
        return hash('sha512', $heslo . $sul);
    }

    /**
     * Zaregistruje nového uživatele
     *
     * @param $data array Registrační údaje
     * @throws MyException Pokud se registrace nezdaří
     */
    public function register ($data) {
        if ($data['password'] != $data['passwordAgain'])
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

            //mb_send_mail("pstechmu@students.zcu.cz", "Testovaci mail", "Toto je testovaci zprava");

            /*$emailSender = new EmailSender();
            $emailSender->sendhello($data['email'], "Hello", "This is a message");
            $emailSender->sendCheckCode($data['email'], $checkCode);*/

        } catch (PDOException $chyba) {
            throw new MyException('Uživatel s tímto jménem je již zaregistrovaný.');
        }
    }

    /**
     * Prihlásí uživatele do systému
     *
     * @param $data array Přihlašovací údaje
     * @throws MyException Pokud se přihlášení nezdaří
     */
    public function login ($data) {
        $username = $data['nick'];
        $password = $data['password'];
        $fromDb = $this->database->queryOne('
                        SELECT user_id
                        FROM users
                        WHERE user_nick = ? AND user_password = ?
                ', [$username, $this->hash($password)]);
        if (!$fromDb)
            throw new MyException('Špatné jméno nebo heslo.');

        $this->database->update('users', ['user_online' => 1, 'user_last_login' => time()], 'WHERE user_id = ?', [$fromDb['user_id']]);
        $_SESSION['user']['id'] = $fromDb['user_id'];
    }

    /**
     * Aktualizuje uživatelská data
     *
     * @param $data array Uživatelská data
     * @return bool True, pokud se aktualizace provedla úspěšně, jinak false
     * @throws MyException
     */
    public function updateUser ($data) {
        $actPass = $this->hash($data['actPassword']);
        /*if (!$_SESSION['user']['user_activated'])
            throw new MyException("Musíte nejdříve aktivovat účet");
        $actPass = $this->hash($data['actPassword']);
        $settings = array();
        if (!empty($data['newNick']))
            $settings['user_nick'] = $data['newNick']; else
            throw new MyException("Přihlašovací jméno nesmí být prázdné");
        if (!empty($data['newPassword']))
            $settings['user_password'] = $this->hash($data['newPassword']);
        if (!empty($data['newEmail']))
            $settings['user_mail'] = $data['newEmail'];
        if ($settings) {
            $uspech = $this->database->update("users", $settings, "WHERE user_id = ? AND user_password = ?", [$_SESSION['user']['user_id'], $actPass]);
            if ($uspech) {
                foreach ($settings as $key => $value)
                    $_SESSION['user'][$key] = $value;
                unset ($_SESSION['user']['user_password']);
                return true;
            }
            throw new MyException("Špatné heslo");
        } else
            throw new MyException("Není co aktualizovat");*/
    }

    /**
     * Aktualizuje uživatelská data
     *
     * @param $data array Pole údajů
     * @return bool True, pokud je aktualizace úspěšná
     * @throws MyException Pokud se aktualizace nepovede
     */
    public function updateUserData ($data) {
        /*if (!$_SESSION['user']['user_activated'])
            throw new MyException("Musíte nejdříve aktivovat účet");
        $actPass = $this->hash($data['actPassword']);
        $settings = array();
        if (!empty($data['name']))
            $settings['user_info_name'] = $data['name'];
        if (!empty($data['age']))
            $settings['user_info_age'] = $data['age'];
        if (!empty($data['motto']))
            $settings['user_info_motto'] = $data['motto'];
        if ($settings) {
            $fromDb = $this->database->queryOne("SELECT COUNT(user_id) FROM users WHERE user_id = ? AND user_password = ?", [$_SESSION['user']['user_id'], $actPass]);
            if (!$fromDb['COUNT(user_id)'])
                throw new MyException("Špatné heslo");
            $uspech = $this->database->update("user_info", $settings, "WHERE user_info_user_id = ?", [$_SESSION['user']['user_id']]);
            if ($uspech) {
                foreach ($settings as $key => $value)
                    $_SESSION['user'][$key] = $value;
                return true;
            } else
                throw new MyException("Aktualizace se nepodařila");
        } else
            throw new MyException("Není co aktualizovat");*/
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
        if ($fromDb)
            return true;
        throw new MyException("Není koho odhlašovat");
    }

    /**
     * Smaže uživatele ze systému
     *
     * @param $heslo string Potvrzovací heslo
     * @return bool True, pokud se smazání povedlo, jinak false
     * @throws MyException Pokud se smazání nepodařilo
     */
    public function delete ($heslo) {
        $id = $_SESSION['user']['user_id'];
        $pass = $this->hash($heslo);
        $emptyUser = array(
            "user_nick" => "Bývalý člen",
            "user_password" => "",
            "user_mail" => "",
            "user_role" => "0",
            "user_online" => "0",
            "user_banned" => "0",
            "user_activated" => "0",
            "user_activation_code" => "",
            "user_name" => "",
            "user_age" => "",
            "user_avatar" => "",
            "user_region" => "",
            "user_city" => "",
            "user_motto" => "",
            "user_skill" => "",

        );
        $fromDb = $this->database->update("users", $emptyUser, "WHERE user_id = ? AND user_password = ?", [$id, $pass]);
        //$fromDb = $this->database->query("DELETE FROM users WHERE user_id = ? AND user_password = ?", [$id, $pass]);
        if ($fromDb) {
            $this->logout(false);
            return true;
        }
        throw new MyException("Špatné heslo");
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