<?php

namespace HongXunPan\Tools\Notice;

class DingTalk{
    public static function send(){

        $ding = new \DingNotice\DingTalk([
            "default" => [
                'enabled' => true,
                'token' => "you-push-token",
                'timeout' => 2.0,
                'ssl_verify' => true,
                'secret' => '',
            ]
        ]);
        
        $ding->text('我就是我, xxx 是不一样的烟火');
    }
}

