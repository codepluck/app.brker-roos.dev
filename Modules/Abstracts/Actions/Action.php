<?php

namespace Modules\Abstracts\Actions;


abstract class Action
{

    public function transaction(...$arguments)
    {
    }

    abstract public function handle($data, $id=null);
}
