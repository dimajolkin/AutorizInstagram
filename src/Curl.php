<?php
/**
 * Created by PhpStorm.
 * User: Develop
 * Date: 20.03.2015
 * Time: 2:49
 */

namespace AutorizInst;


use Zend\Dom\Query;


class Curl {

    private $ch;
    private $url;

    private $redirect_url;
    private $client_id;
    private $post;
    private $param;

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }



    private function getSetting()
    {
        return include "setting.php";
    }


    function __construct($config)
    {
        //Берём параметры приложения
        $this->redirect_url = $config['redirect_uri'];
        $this->client_id = $config['client_id'];


        $this->ch = curl_init();

        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3";
        $header[] = "Pragma: "; // browsers keep this blank.
        $header[] = "Content-Type:application/x-www-form-urlencoded";
        $header[] = "Origin:https://instagram.com";
//        $header[] = "Referer:
//        https://instagram.com/accounts/login/?force_classic_login=&next=
//        /oauth/authorize/%3Fclient_id%3D467311be2dc34185bbde5de4cbd10ead%26redirect_uri%3Dhttp%253A%252F%252Fwww.uostapb.bget.ru%252Fapplication%252Findex%252Freg%26response_type%3Dcode";

        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);

        curl_setopt($this->ch, CURLOPT_USERAGENT, Browser::Mozila());

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0); //? проверяет сертификат
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

        $this->param = self::getSetting();
        if(isset($this->param['url_form'])){
            $result = $this->param['url_form'];
            $url = $result($this->client_id,$this->redirect_url);
            $header[] = 'Referer:'.$url;
        } else throw new Exception("");


    }

    public function setForm($data)
    {
        $this->post = $data;

    }

    public function setFileCookie($filame)
    {
        $tmpfname = __DIR__.'/../../../../../'.$filame.'.txt';
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $tmpfname);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $tmpfname);
    }

    public function run()
    {
        //Step 1
        $func = $this->param['url'];
        $url = $func($this->client_id,$this->redirect_url);
        var_dump($url);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $html_autoriz =  curl_exec(  $this->ch );

        $this->post['csrfmiddlewaretoken'] = '';
        //Теперь надо вытащить кодовое слово со страницы
        if(empty($html_autoriz)){
            echo "Error Connect Step 1";
            die();
        }

        echo $html_autoriz;
        die();

        $dom = new Query($html_autoriz);
        $list =  $dom->execute("form input");
        foreach($list as $elem)
        {
            if( $elem->getAttribute("name") == 'csrfmiddlewaretoken') {
                $this->post['csrfmiddlewaretoken'] = $elem->getAttribute("value");
                break;
            }
        }

        var_dump($this->post);


    }







}