<?php namespace Awebsome\PrivatePlugins\Classes;

use Config;
use Flash;
use Storage;
use Lang;
use ValidationException;

use October\Rain\Filesystem\Zip;

/**
 *
 */
class FileUploader
{
    public $url;
    public $path;
    public $basePath;
    public $downloadDirectory;

    const PLUGINS_PATH = '/plugins';
    const DOWNLOAD_PATH = '/plugins/awebsome/privateplugins/downloads/';

    public static function download($url, $path = null, $basePath = null)
    {
        $self = new self;

        $self->url = $url;
        $self->path = $path;
        $self->basePath = $basePath;

        return $self->downloadFile();
    }

    public function downloadFile()
    {
        $basePath = $this->basePath;
        $path = $this->path;
        $url = $this->url;

        //set default tmp destination folder
        if(empty($basePath))
            $basePath = base_path().self::DOWNLOAD_PATH;

        $this->downloadDirectory = $path ? $basePath.$path.'/' : $basePath;

        //set Remote File URL
        $remote_file_url = trim($url);

        if (!is_dir($this->downloadDirectory))
        {
            $downloadDirectory = mkdir($this->downloadDirectory, 0777, true); // @todo Use config
            if ($downloadDirectory === false) {
                throw new ValidationException(['error_message'=> 'Failed to get create temporary directory: %s '. $this->tempDirectory]);
            }
        }

        //File Name To Download.
        $local_file = basename($remote_file_url);

        //Destination File

        $newLocalFile = $this->downloadDirectory.$local_file;

        $newFile = fopen($newLocalFile, "wb");

        if($newFile){
            if(!@copy( $remote_file_url, $newLocalFile ))
            {
                throw new ValidationException(['error_message'=> 'Failed to download the plugin repo_url error '. $url]);
            } else {
                return $newLocalFile;
            }
        }else {
            throw new ValidationException(['error_message'=>'Failed to create file: %s ']);
        }
    }

    public static function unzip($zip, $path_name, $vendor, $name, $repo_name)
    {
        /**
         * @var $path vendor-plugin
         * @var $zip downloaded file
         */

        # Get File Name Zip.
        $zipName = basename($zip);

        # File downloaded path /vendor-plugin/master.zip
        $zipPath = self::DOWNLOAD_PATH.$path_name.'/'.$zipName;

        # Storage Disk
        $Storage = Storage::disk('oc_local_root'); #storage path include base_path() root

        # Path where the plugin is unziped. plugins/vendor
        $vendorPath = self::PLUGINS_PATH.'/'.$vendor;

        $newZipPath = $vendorPath.'/'. $path_name.'-'.$zipName;

        $futurePath = $vendorPath.'/'.$name;

        if($Storage->allFiles($futurePath))
            $Storage->deleteDirectory($futurePath);

        if($Storage->exists($newZipPath))
            $Storage->delete($newZipPath);

        if($Storage->move($zipPath, $unzipFile = $newZipPath))
        {

            # UnZip the plugin
            if (!Zip::extract(base_path().$unzipFile, base_path().$vendorPath)) {
                throw new ValidationException([
                       'error_message' => Lang::get('system::lang.zip.extract_failed', ['file' => $newFile])
                    ]);
            }{
                $Storage->move($vendorPath.'/'.$repo_name.'-master', $futurePath);
            }

        }else throw new ValidationException([
               'error_message' => "An error has ocurred in the copy proccess"
            ]);



        $Storage->delete($unzipFile);

        return true;
    }
}
