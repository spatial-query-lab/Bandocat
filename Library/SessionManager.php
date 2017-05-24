<?php
class SessionManager
{
    private $userName;
    private $userID;
    private $role;
    private $logged_in;

    /**
     * SessionManager constructor.
     * @param $userName
     * @param $userID
     * @param $role
     */
    public function __construct()
    {
        session_start();
        if(isset($_SESSION['username']) && isset($_SESSION['userID']) || isset($_SESSION['role'])) {
            $this->userName = $_SESSION['username'];
            $this->userID = $_SESSION['userID'];
            $this->role = $_SESSION['role'];
        }
        else header('Location: ../../Forms/Login/');

        if (time() > $_SESSION['end']) {
            $this->setLoggedIn(false);
            session_destroy();
            header('Location: ../../Forms/Login/?keyword=sessionexpired');
        }


        if($this->userName == null || $this->userName == ""
             || $this->userID == null || $this->userID == ""
            || $this->role == null || $this->role == "") {
            $this->setLoggedIn(false);
        }
        else $this->setLoggedIn(true);
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getLoggedIn()
    {
        return $this->logged_in;
    }

    /**
     * @param mixed $logged_in
     */
    public function setLoggedIn($logged_in)
    {
        $this->logged_in = $logged_in;
    }


    //Super admin & admin
    public function isAdmin()
    {
        if($this->getRole() == 'Admin' || $this->getRole() == 'Super Admin')
            return true;
        return false;
    }

    //Super admin
    public function isSuperAdmin()
    {
        if($this->getRole() == 'Super Admin')
            return true;
        return false;
    }

    //can write/edit review documents
    public function hasWritePermission()
    {
        if($this->getRole() == 'Admin' || $this->getRole() == 'Super Admin' || $this->getRole() == 'Writer')
            return true;
        return false;
    }

    public function unblockSession()
    {
        session_write_close();
    }



}