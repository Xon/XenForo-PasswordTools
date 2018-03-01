<?php

class KL_PasswordTools_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'KL_PasswordTools_' . $class;
    }
}
