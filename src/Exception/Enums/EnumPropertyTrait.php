<?php

namespace HongXunPan\Tools\Exception\Enums;

Trait EnumPropertyTrait
{
    public function getProperty($property)
    {
        if (!isset(self::PROPERTY[$this->value])) {
            return null;
        }
        if (is_array(self::PROPERTY[$this->value])) {
            return self::PROPERTY[$this->value][$property] ?? null;
        }
        return self::PROPERTY[$this->value];
    }
}
