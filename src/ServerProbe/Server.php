<?php

namespace HongXunPan\Tools\ServerProbe;

class Server
{
    //获取硬件信息
    public function hardwareInfo()
    {

    }

    //实时信息
    public function liveInfo()
    {
        //time
        //top
        //cpu info
        //cpu usage
        //disk info & usage &
        //memory info & usage & swag
        //load 1 2 5
    }

    public function netInfo()
    {
        //eth
        //speed
    }

    public function phpInfo()
    {
        phpinfo();
        //get php -m
    }
}