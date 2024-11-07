<?php

namespace HongXunPan\Tools\Exception\Enums;

enum EnumException: string
{
    use EnumPropertyTrait;
    case FORBIDDEN = '403';
    case UNKNOWN = '500';
    case VALIDATE = '400';
    case NOT_FOUND = '404';
    case NOT_ALLOWED = '405';
    case NOT_IMPLEMENTED = '501';

    const PROPERTY = [
        '404' => ['msg' => 'not found', 'code' => 404],
    ];
}
