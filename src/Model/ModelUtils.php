<?php

namespace HongXunPan\Tools\Model;

class ModelUtils
{
    //给指定类增加注释
    public static function addClassComment($className, $comment)
    {
        //判断类是继承自 Illuminate\Database\Eloquent\Model
        if (!is_subclass_of($className, 'Illuminate\Database\Eloquent\Model')) {
            return;
        }
        //写入注释
        $class = new \ReflectionClass($className);
        $file = $class->getFileName();
        $lines = file($file);
        $lines[0] = "<?php\n/**\n * {$comment}\n */\n";
        file_put_contents($file, implode('', $lines));
    }
}