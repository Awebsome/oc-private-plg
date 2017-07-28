<?php namespace Awebsome\PrivatePlugins\Classes;

use Storage;
use ValidationException;
use Awebsome\PrivatePlugins\Classes\FilesystemHelper;

class PluginCode
{
    public $Storage;

    public function __construct($PluginAutoload = true)
    {
        $this->Storage = Storage::disk('oc_local_root');
        $this->plugin = $PluginAutoload;
    }

    public function getNamespace()
    {
        //Namespace Error plugins/awebsome/privateplugins/tmp/57ed5e01985b3846065605/Plugin.php

        $src = $this->Storage->get($this->plugin);
        $tokens = token_get_all($src);
        throw new ValidationException([
               'error_message' => "Namespace Error " . $this->plugin
            ]);
    }
}
