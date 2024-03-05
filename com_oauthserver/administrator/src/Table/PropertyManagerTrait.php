<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Table;

use Webmasterskaya\Component\OauthServer\Administrator\Helper\CasesHelper;

trait PropertyManagerTrait
{
    public function __get($name)
    {
        // All protected properties names must start with _
        if (str_starts_with($name, '_')) {
            return null;
        }

        // If class has getter for property
        $getterName = 'get' . CasesHelper::camelize($name, true);
        if (method_exists($this, $getterName)) {
            return call_user_func([$this, $getterName]);
        }

        if (isset($this->$name)) {
            return $name;
        }

        return null;
    }

    public function __set($name, $value)
    {
        // All protected properties names must start with _
        if (str_starts_with($name, '_')) {
            return;
        }

        // If class has setter for property
        $setterName = 'set' . CasesHelper::camelize($name, true);
        if (method_exists($this, $setterName)) {
            call_user_func([$this, $setterName], $value);
            return;
        }

        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}