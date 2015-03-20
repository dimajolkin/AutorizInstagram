<?php
/**
 * Created by PhpStorm.
 * User: Develop
 * Date: 20.03.2015
 * Time: 16:08
 */

namespace AutorizInst;


class User {
    private $username;
    private $password;

    function __construct($username,$password)
    {
        $this->password = $password;
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }



} 