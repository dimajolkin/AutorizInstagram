<?php

include_once "../autoload.php";


use AutorizInst\Autoriz;

$config =  array(
    'app'=>array(
        'default'=>array(
            'client_id'=> '467311be2dc34185bbde5de4cbd10ead',
            'client_secret'=>'47f71e33dfb642b7a6e3a4611dfa2a3a',
            'redirect_uri'=>'http://www.uostapb.bget.ru/application/index/reg',
            'scope'=>array('basic')
        ),
        'test4'=>array(
            'client_id'=> '2e9db5d10540451a8e8cabc5434b0300',
            'client_secret'=>'bf951e35f27845c5989e92d6748bc084',
            'redirect_uri'=>'http://www.uostapb.bget.ru/application/index/gettokken',
            'scope'=>array('basic')
        ),

    )
);



$user = new \AutorizInst\User("dimajolkin","11111");

$aut = new Autoriz( $config['app']['test4'] );
$aut->setUser($user);
$res = $aut->autorize();
if($res){
    $tokken = trim($res);



}

