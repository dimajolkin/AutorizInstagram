<?php
/**
 * Created by PhpStorm.
 * User: Develop
 * Date: 20.03.2015
 * Time: 2:49
 */

namespace AutorizInst;

class Curl {

    private $ch;
    private $url;

    private $redirect_url;
    private $client_id;
    private $post;
    private $param;
    private $message;
    private $header;
    private $scope;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


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
        $this->scope = $config['scope'];


        if(function_exists("curl_init")){
            $this->ch = curl_init();
        } else throw new \Exception("Curl Не подключен!");

        $this->header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $this->header[0].= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $this->header[]  = "Cache-Control: max-age=0";
        $this->header[]  = "Connection: keep-alive";
        $this->header[]  = "Keep-Alive: 300";
        $this->header[]  = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $this->header[]  = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3";
        $this->header[]  = "Pragma: "; // browsers keep this blank.
        $this->header[]  = "Content-Type:application/x-www-form-urlencoded";
        $this->header[]  = "Origin:https://instagram.com";


        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);

        curl_setopt($this->ch, CURLOPT_USERAGENT, Browser::Mozila());

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0); //? проверяет сертификат
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

        $this->param = self::getSetting();
        if(isset($this->param['url_form'])){
            $result = $this->param['url_form'];
            $url = $result($this->client_id,$this->redirect_url);
            $this->header[] = 'Referer:'.$url;
        } else throw new \Exception("");

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);

    }

    public function setForm($data)
    {
        $this->post = $data;

    }

    public function setFileCookie($filame)
    {
        $tmpfname = $filame.'.txt';
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $tmpfname);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $tmpfname);
    }


    public function run()
    {


        //Step 1
        //Ссылка на форму авторизации
        $func = $this->param['url'];
        $url = $func($this->client_id,$this->redirect_url);

        //ссылка на форме атирибут Аction
        $func = $this->param['url_form'];
        $url_form = $func($this->client_id,$this->redirect_url);


        curl_setopt($this->ch, CURLOPT_URL, $url);
        $html_autoriz =  curl_exec(  $this->ch );


        $this->post['csrfmiddlewaretoken'] = '';
        //Теперь надо вытащить кодовое слово со страницы
        if(empty($html_autoriz)){
            echo 'Error Connect Step 1';
            die();
        }

        //Загружаем полученную страницу
        /**
         * @var $html \simple_html_dom
         */
        $html = str_get_html($html_autoriz);
        $list = $html->find('form input[name=\'csrfmiddlewaretoken\']');
        foreach($list as $elem){
            $this->post['csrfmiddlewaretoken'] = $elem->attr['value'];
        }

        /**
         * Шаг второй. после подготовки данных и получения начальных куков
         * необходимо отправить данные на сервер для авторизации
         * Так же осуществляем проверку на корректность данных
         */
        $this->post = http_build_query($this->post);



        curl_setopt($this->ch, CURLOPT_URL, $url_form );

        if($this->post)
        {
            curl_setopt($this->ch, CURLOPT_POST, $this->post ? 0 :1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS,$this->post);
        }


        $html_autoriz  =  curl_exec($this->ch);

        //  var_dump($html_autoriz);

        $html = str_get_html($html_autoriz);
        //Осуществляем поиск сообщения об ошибке
        //Если не нашли продолжаем искать
        $list = $html->find('form ul[class=\'errorlist\'] li');
        $message = true;
        foreach($list as $elem){
            $message = $elem;
        }

        //Если мы нашли ошибку
        if(!$message){
            self::setMessage($message);
            return false;
        }

        //Выполняем поиск кнопок подтверждения
        $list = $html->find('form input[name=\'allow\']');
        $message = true;
        foreach($list as $elem){
            if($elem->attr['value'] === 'Cancel'){
                $message = false;
            }
        }



        if(!$message){
            /**
             * Выполняем подтвержение пользоваться нашимы регистарционными данными
             */

            $func = $this->param['url_assets'];
            $url = $func($this->client_id,$this->redirect_url, $this->scope);

            $this->header[ count($this->header)-1 ] = 'Referer:'.$url;

            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($this->ch, CURLOPT_URL, $url );


            $this->post = array();
            $list = $html->find('form input[name=\'csrfmiddlewaretoken\']');
            foreach($list as $elem){
                $this->post['csrfmiddlewaretoken'] = $elem->attr['value'];
            }

            $this->post['allow'] = 'Authorize';
            $post_assets = http_build_query($this->post);

            if($post_assets)
            {
                curl_setopt($this->ch, CURLOPT_POST, $post_assets ? 0 :1);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_assets);
            }

            $html = curl_exec($this->ch);

            echo $html;

            return false;
        }

        return true;




    }








}