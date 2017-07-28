<?php namespace Awebsome\PrivatePlugins\Models;

use Model;
use Crypt;
use Flash;
use ValidationException;
use October\Rain\Filesystem\Zip;
use Illuminate\Contracts\Encryption\DecryptException;

use Awebsome\PrivatePlugins\Classes\AuthorHelper;
use Awebsome\PrivatePlugins\Classes\FileUploader;
use Awebsome\PrivatePlugins\Classes\FilesystemHelper;

/**
 * Plugin Model
 */
class Plugin extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_privateplugins_plugins';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = ['plugin' => 'System\Models\File'];
    public $attachMany = [];


   /**
    * @var array Validation rules
    */
    protected $rules = [
        'code' => ['required'],
        'repo_name' => ['required','alpha_dash'],
    ];

    public function beforeSave()
    {
        $this->downloadPlugin();
    }

    public function downloadPlugin()
    {
        //Url with password decrypt
        $url = str_replace('xxxxxxx', $this->passwordDecrypt(), $this->repo_url);
        $download_in = $this->path_name;

        $zip = FileUploader::download($url, $download_in);

        FileUploader::unzip($zip, $download_in, $this->vendor, $this->name, $this->repo_name);
    }



    /**
     * Get Path Name (directory name)
     */
    public function getPathNameAttribute()
    {
        $path_name = strtolower($this->code);
        $path_name = str_replace('.', '-', $path_name);
        return $path_name;
    }

    /**
     * Repo URL by Provider
     */
    public function getRepoUrlAttribute()
    {
        if($this->repository == 'github')
            return $this->private ? $this->github_private_repo : $this->github_repo;
    }

    /**
     * Github Repo URL
     */
    public function getGithubRepoAttribute()
    {
        return $url = 'https://github.com/'.$this->vendor.'/'.$this->repo_name.'/archive/master.zip';
    }

    /**
     * Github Private Repo URL
     */
    public function getGithubPrivateRepoAttribute()
    {
        return $url = 'https://'.$this->user.':xxxxxxx@github.com/'.$this->vendor.'/'.$this->repo_name.'/archive/master.zip';
    }

    /**
     * Get Vendor Name
     */
    public function getVendorAttribute()
    {
        return strtolower(AuthorHelper::plugin($this->code)->vendor);
    }

    /**
     * Get Plugin Name
     */
    public function getNameAttribute()
    {
        return strtolower(AuthorHelper::plugin($this->code)->name);
    }

    /**
     * Set Password Encryption.
     *
     * @param  string  $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        if($value)
            $this->attributes['password'] = Crypt::encrypt($value);
    }

    /**
     * Get Password Decrypt.
     *
     * @return string
     */
    public function passwordDecrypt()
    {
        try {
            return Crypt::decrypt($this->password);
        }
        catch (DecryptException $ex) {
            return null;
        }

    }
}
