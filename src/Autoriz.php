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
use ZendTest\XmlRpc\Server\Exception;

class Autoriz {


private  $curl;

    function __construct($config)
    {

            $this->curl = new Curl($config);
            $this->curl->setFileCookie("fd");
            $this->curl->setForm(array(
                'username' => '12',
                'password'=> 'af160035109a'
            ));


            $this->curl->run();





    }


}