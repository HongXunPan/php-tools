<?php

namespace HongXunPan\Tools\ServerMonitor;

class Client{

    public static function ping(){
        $res = Server::pong(); //todo curl http 
        if($res == 'pong'){
            return true;
        }
        return false;
    }

}
