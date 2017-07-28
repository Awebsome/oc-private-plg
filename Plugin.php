<?php namespace Awebsome\PrivatePlugins;

use Config;
use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {
        /**
         * Set Filesystem Configs to use for this plugin
         */
        Config::set('filesystems.disks.oc_local_root.driver', 'local');
        Config::set('filesystems.disks.oc_local_root.root', base_path());
    }

    public function registerComponents()
    {

    }

    public function registerSettings()
    {
      return [

          //Connection Settings
          'plugins'  => [
              'label'       => 'Private Plugins',
              'description' => 'Private Plugins',
              'category'    => 'system::lang.system.categories.system',
              'icon'        => 'icon-puzzle-piece',
              'url'         => Backend::url('awebsome/privateplugins/plugins'),
              'order'       => 100,
              'permissions' => [ 'awebsome.privateplugins.plugins' ],
              'keywords'    => 'Update Private Plugins'
          ],
      ];
    }
}
