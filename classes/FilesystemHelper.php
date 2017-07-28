<?php namespace Awebsome\PrivatePlugins\Classes;

use Lang;
use Flash;

use Config;
use Storage;
use ValidationException;

use System\Classes\PluginManager;

use Awebsome\PrivatePlugins\Models\Plugin;
use Awebsome\PrivatePlugins\Classes\PluginCode;
use October\Rain\Filesystem\Zip;

class FilesystemHelper
{

    public function __construct($plugin = null)
    {
        # Load From Model
        $this->plugin = $plugin;

        # Route where the plugin is downloaded per first time as temporal file
        $this->tmp_path = Config::get('awebsome.privateplugins::tmp_path');

        # Storage Disk
        $this->Storage = Storage::disk('oc_local_root');
    }

    public function uploadFile()
    {
        $file = $this->plugin;

        # Path where the plugin is unziped.
        $copyToPath = $this->tmp_path . $this->getFolderName($file->disk_name);

        $copyFileAs = $copyToPath . $file->disk_name;

        if(!$this->Storage->exists($copyFileAs))
        {
            if($this->Storage->copy($file->path, $newFile = $copyFileAs))
            {

                # UnZip the plugin
                if (!Zip::extract($newFile, $copyToPath)) {
                    throw new ValidationException([
                           'error_message' => Lang::get('system::lang.zip.extract_failed', ['file' => $newFile])
                        ]);

                }

            }else throw new ValidationException([
                   'error_message' => "An error has ocurred in the copy proccess"
                ]);

        }else throw new ValidationException([
               'error_message' => "The Plugin already exists"
            ]);

        $this->Storage->delete($newFile);


        if($loadPlugin = $this->loadPlugin($copyToPath)){
            Flash::success('Plugin Loaded'. json_encode($loadPlugin));
        }
    }

    public function getFolderName($fileName)
    {
        return str_replace(".zip",'/', $fileName);
    }

    public function loadPlugin($loadPath)
    {
        $PluginFile = $loadPath . 'Plugin.php';

        if ($this->Storage->exists($PluginFile))
        {
            $PluginCode = new PluginCode ($PluginFile);
            return $PluginCode->getNamespace();

        } else throw new ValidationException([
               'error_message' => "The Plugin.php not found"
            ]);

    }
}
