<?php

namespace app\model\manager;

use app\model\database\Database;
use app\model\factory\UserFactory;
use app\model\User;
use app\model\util\SimpleImage;
use Exception;
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
     * @param $heslo
     * @return string
     */
    public function hash ($heslo) {
        $sul = '~e_;*G=_;G4T%;n;4V*D#$%';
        return hash('sha512', $heslo . $sul);
    }

    /**
     * Zaregistruje nového uživatele.
     * @param $data array Registrační údaje
     * @return mixed
     * @throws Exception
     */
    public function register ($data) {
        if ($data['password'] != $data['passwordAgain'])
            throw new Exception('Hesla nesouhlasí.');
        $pass = $this->hash($data['password']);
        $checkCode = str_shuffle($this->hash($pass));
        $user = array(
            'user_nick' => $data['username'],
            'user_password' => $pass,
            'user_mail' => $data['email'],
            'user_first_login' => time(),
            'user_activation_code' => $checkCode,
            'user_avatar' => "empty_image"
        );
        try {
            $this->database->insert('users', $user);
            //$userID = Db::getLastId();
            //$this->databaze->insert("user_info", ['user_info_user_id' => $userID]);

            mb_send_mail("pstechmu@students.zcu.cz", "Testovaci mail", "Toto je testovaci zprava");

            /*$emailSender = new EmailSender();
            $emailSender->sendhello($data['email'], "Hello", "This is a message");
            $emailSender->sendCheckCode($data['email'], $checkCode);*/

        } catch (PDOException $chyba) {
            throw new Exception('Uživatel s tímto jménem je již zaregistrovaný.');
        }
    }

    /**
     * Prihlásí uživatele do systému.
     * @param $data array Přihlašovací údaje.
     * @throws Exception
     */
    public function login ($data) {
        $username = $data['nick'];
        $password = $data['password'];
        $fromDb= $this->database->queryOne('
                        SELECT user_id
                        FROM users
                        WHERE user_nick = ? AND user_password = ?
                ', [$username, $this->hash($password)]);
        if (!$fromDb)
            throw new Exception('Špatné jméno nebo heslo.');

        $this->database->update('users', ['user_online' => 1, 'user_last_login' => time()], 'WHERE user_id = ?', [$fromDb['user_id']]);
        /*$_SESSION['user'] = $user;
        $_SESSION['user']['user_online'] = 1;*/
        $_SESSION['user']['id'] = $fromDb['user_id'];
    }

    /**
     * Aktualizuje uživatelská data.
     * @param $data array Uživatelská data.
     * @return bool True, pokud se aktualizace provedla úspěšně, jinak false.
     * @throws Exception
     */
    public function updateUser ($data) {
        /*if (!$_SESSION['user']['user_activated'])
            throw new Exception("Musíte nejdříve aktivovat účet");
        $actPass = $this->hash($data['actPassword']);
        $settings = array();
        if (!empty($data['newNick']))
            $settings['user_nick'] = $data['newNick']; else
            throw new Exception("Přihlašovací jméno nesmí být prázdné");
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
            throw new Exception("Špatné heslo");
        } else
            throw new Exception("Není co aktualizovat");*/
    }

    /**
     * Aktualizuje uživatelská data.
     * @param $data array Pole údajů.
     * @return bool True, pokud je aktualizace úspěšná.
     * @throws Exception Pokud se aktualizace nepovede.
     */
    public function updateUserData ($data) {
        /*if (!$_SESSION['user']['user_activated'])
            throw new Exception("Musíte nejdříve aktivovat účet");
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
                throw new Exception("Špatné heslo");
            $uspech = $this->database->update("user_info", $settings, "WHERE user_info_user_id = ?", [$_SESSION['user']['user_id']]);
            if ($uspech) {
                foreach ($settings as $key => $value)
                    $_SESSION['user'][$key] = $value;
                return true;
            } else
                throw new Exception("Aktualizace se nepodařila");
        } else
            throw new Exception("Není co aktualizovat");*/
    }

    /**
     * Změní obrázek uživateli na nový
     * @param $avatar array Pole informací o obrázku
     * @return string Relativní cestu k obrázku
     * @throws Exception Pokud se změna obrázku nepovedla
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
            throw new Exception("Změna obrázku se nepovedla");

        $this->filemanager->recursiveDelete($avatarDir . $user->getAvatar() . ".png");

        return $this->filemanager->getRelativePath($avatarDir . $avatarName . ".png");
    }

    /**
     * Odhlásí uživatele ze systému.
     * @param bool $changeDb Pokud je true, změní se údaj v databázi.
     * @return bool True, pokud je odhlášení v pořádku.
     * @throws Exception Pokud se odhlášení nezdařilo.
     */
    public function logout ($changeDb = true) {
        $fromDb = null;
        if ($changeDb)
            $fromDb = $this->database->update('users', ['user_online' => 0], 'WHERE user_id = ?', [$_SESSION['user']['id']]);
        //session_destroy();
        unset($_SESSION['user']);
        if ($fromDb)
            return true;
        throw new Exception("Není koho odhlašovat");
    }

    /**
     * Smaže uživatele ze systému.
     * @param $heslo string Potvrzovací heslo.
     * @return bool True, pokud se smazání povedlo, jinak false.
     * @throws Exception
     */
    public function delete ($heslo) {
        $id = $_SESSION['user']['user_id'];
        $pass = $this->hash($heslo);
        $fromDb = $this->database->query("DELETE FROM users WHERE user_id = ? AND user_password = ?", [$id, $pass]);
        if ($fromDb) {
            $this->logout(false);
            return true;
        }
        throw new Exception("Špatné heslo");
    }

    /**
     * Vrátí uživatelská data pokud existují.
     * @param $userID int Volitelný parametr. Pokud je nula, vrátí přihlášeného uživatele, jinak výpis o jiném uživateli.
     * @return User Třídu uživatele
     * @throws Exception
     */
    public function getUser ($userID = 0) {
        if (!$userID && !isset($_SESSION['user']))
            throw new Exception("Uživatel nenalezen");

        if (!$userID)
            $userID = $_SESSION['user']['id'];

        return $this->userfactory->getUserByID($userID);
    }

    /**
     * Vrátí true, pokud je uživatel přihlášen.
     * @return bool True, pokud je uživatel přihlášený, jinak false
     */
    public function isLoged () {
        return isset($_SESSION['user']);
    }

    /**
     * Vrátí true, pokud je účet aktivovaný, jinak false.
     * @return bool True, pokud je účet aktivovaný, jinak false.
     * @throws Exception Pokud není uživatel přihlášen.
     */
    public function isActivated () {
        if ($_SESSION['user']['user_activated'])
            return $_SESSION['user']['user_activated'];

        throw new Exception("Uživatel není prihlášený.");
    }

    /**
     * Ověří aktivační kód.
     * @param $code string Aktivační kód.
     * @return bool True, pokud se aktivace zdařila, jinak false.
     * @throws Exception
     */
    public function checkCode ($code) {
        $fromDb = $this->database->update("users", ["user_activated" => 1], "WHERE user_activation_code = ?", [$code]);

        if ($fromDb) {
            if ($_SESSION['user'])
                $_SESSION['user']['activated'] = 1;
            return true;
        }
        throw new Exception("Kód nelze ověřit");
    }
}