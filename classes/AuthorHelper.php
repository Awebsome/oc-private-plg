<?php namespace Awebsome\PrivatePlugins\Classes;

use Storage;

class AuthorHelper
{
    public $Storage;
    public $vendor;
    public $name;

    public function __construct()
    {
        //$this->Storage = Storage::disk('oc_local_root');
    }

    public static function plugin($code)
    {
        $self = new Self;

        if($code)
        {
            $nameSpace = explode('.', $code);
        }

        if(is_array($nameSpace))
        {
            $self->vendor   = current($nameSpace);
            $self->name     = end($nameSpace);
        }

        return $self;
    }
}
