<?php
return array(
    'url'=> function($client_id, $redirect_uri){
        return sprintf('https://api.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code',
            urlencode($client_id),
            urlencode($redirect_uri)
        );
    },
    'url_form'=> function($client_id, $redirect_uri){
        $t = sprintf('/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code',
            urlencode($client_id),
            urlencode($redirect_uri)
        );
        $url_form = 'https://instagram.com/accounts/login/?force_classic_login=&next='.urlencode($t);
        return $url_form;
    }


);