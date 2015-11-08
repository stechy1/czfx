<?php

namespace app\model;


use Exception;

class UserRole {

    const
        OLD_MEMBER = 1,
        MEMBER = 2,
        REDACTOR = 3,
        ADMIN = 10;

    private $roleID;

    /**
     * Role constructor.
     * @param $roleID
     */
    public function __construct ($roleID) {
        $this->roleID = $roleID;
    }

    /**
     * Zvaliduje oprávnění
     *
     * @param $requiredRole int Požadované oprávnění.
     * @param bool $throwException True, pokud se v případě neúspěchu má vyvolat vyjímka, jinak se vrátí false.
     * @return bool True, pokud je oprávnění v pořádku.
     * @throws Exception
     */
    public function valid ($requiredRole, $throwException = true) {
        if ($this->roleID >= $requiredRole)
            return true;

        if ($throwException)
            throw new Exception("Nedostatečná oprávnění");

        return false;
    }

    function __toString () {
        return 'role';
    }


}