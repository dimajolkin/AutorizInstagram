<?php
/**
 * Created by PhpStorm.
 * User: Develop
 * Date: 20.03.2015
 * Time: 2:38
 */
namespace AutorizInst;

/**
 * Авторизация в Инстаграмее по логину с паролем
 * Автоматически отправляет данные на сервер авторизации и
 * получает токен
 *
 * Использует curl
 * Class Autoriz
 * @package AutorizInst
 */
use AutorizInst\Curl;

class Autoriz {


    private  $curl;
    private $user;
    private $token;


    function __construct($config)
    {

        try{
            $this->curl = new Curl($config);
            $this->curl->setFileCookie("fd");

        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
    public function setUser($user)
    {

        try{
            if(!is_object($user))
            {
                if(is_array($user)){
                    if(isset($user['username']) and isset($user['password']))
                    {
                        $this->user = $user;

                    } else  throw new \Exception("Autoriz::setUset() не верный входной параметр");

                } else throw new \Exception("Autoriz::setUset() не верный входной параметр");

            }else {
                /**
                 * @var $user User
                 */
                if($user instanceof User)
                {
                    $this->user = array(
                        'username'=>$user->getUsername(),
                        'password'=>$user->getPassword()
                    );
                } else {
                    throw new \Exception("Autoriz::setUset() не верный входной параметр");
                }
            }

        }catch (\Exception $e){
            echo $e->getMessage();
        }

    }

    /**
     * <p>
     * Авторизовывает пользоватедя возвращая его токкен
     * если вернул false авторизация не удалась
     *
     * @return bool
     */
    public function autorize()
    {
        $this->curl->setForm(
            $this->user
        );

        return $this->curl->run();
    }


}