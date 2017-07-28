<?php namespace Awebsome\PrivatePlugins\Controllers;

use Backend\Controllers\Files;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use Storage;
use Response;
use File;
use Config;
use Awebsome\PrivatePlugins\Classes\FilesystemHelper;
use Awebsome\PrivatePlugins\Classes\FileUploader;
use October\Rain\Filesystem\Zip;

/**
 * Plugins Back-end Controller
 */
class Plugins extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Awebsome.PrivatePlugins', 'plugins');

    }

    public function test()
    {
    }
}
