<?php
/**
 * Storage Service
 * 
 * @copyright (c) 2013, Core.net
 * @author Berdimurat Masaliev <muratmbt@gmail.com>
 */

namespace Akazakbaev\LaminasStorage\Storage;

use Akazakbaev\LaminasStorage\Entity\StorageFiles;

class Local extends AbstractService
{
        protected $type;

        protected $_path;

        protected $_baseUrl;

    public function __construct(array $config)
    {
        if( !empty($config['path']) )
        {
            $this->_path = $config['path'];
        }
        else
        {
            $this->_path = 'files';
        }

        if( !empty($config['baseUrl']) ) {
            $this->_baseUrl = $config['baseUrl'];
        }
    }

    public function getBaseUrl()
    {
        if (null === $this->_baseUrl) {
            $this->_baseUrl = $this->_removeScriptName($this->url());
        }
        return $this->_baseUrl;
    }

    // Accessors

    public function map(StorageFiles $model)
    {
        return rtrim($this->getBaseUrl(), '/') . '/' . $model->getStoragePath();
    }

    public function store($model, $file)
    {
        $path = $this->getScheme()->generate($model->toArray());

        try {
            $this->_mkdir(dirname(APPLICATION_PATH . DS . 'public'. DS . $path));
            $this->_copy($file, APPLICATION_PATH . DS . 'public'. DS . $path);
            @chmod(APPLICATION_PATH . DS . 'public'. DS . $path, 0777);
        } catch (\Exception $e) {
            @unlink(APPLICATION_PATH . DS . 'public'. DS . $path);
            throw $e;
        }

        return $path;
    }

    public function read($model) {
        $file = APPLICATION_PATH . '/' . $model->storage_path;
        return @file_get_contents($file);
    }

    public function write($model, $data) {
        // Write data
        $path = $this->generatePath($model->toArray());

        try {
            $this->_mkdir(dirname(APPLICATION_PATH . '/' . $path));
            $this->_write(APPLICATION_PATH . '/' . $path, $data);
            @chmod($path, 0777);
        } catch (\Exception $e) {
            @unlink(APPLICATION_PATH . '/' . $path);
            throw $e;
        }

        return $path;
    }

    public function remove($model) {
        if (!empty($model->storage_path)) {
            $this->_delete(APPLICATION_PATH . '/' . $model->storage_path);
        }
    }

    public function temporary($model)
    {
        $file = APPLICATION_PATH . '/' . $model->storage_path;
        $tmp_file = APPLICATION_PATH . '/files/temporary/' . basename($model['storage_path']);
        $this->_copy($file, $tmp_file);
        @chmod($tmp_file, 0777);
        return $tmp_file;
    }

    public function removeFile($path) {
        $this->_delete($path);
    }
    
}

?>
