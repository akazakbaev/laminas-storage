<?php

namespace Akazakbaev\LaminasStorage\Storage;

use Akazakbaev\LaminasStorage\Adapter\AbstractVfsAdapter;
use Akazakbaev\LaminasStorage\Classes\VfsFactory;
use Akazakbaev\LaminasStorage\Entity\StorageFiles;
use Akazakbaev\LaminasStorage\Exception\StoreException;

class Vfs extends AbstractService
{
  // General

  protected $_type = 'vfs';

  protected $_baseUrl;

  /**
   * @var Engine_Vfs_Adapter_Abstract
   */
  protected $_vfs;

  protected $_vfsConfig;

  public function  __construct(array $config = array())
  {
    // Get baseUrl
    if( isset($config['baseUrl']) ) {
      $this->_baseUrl = rtrim($config['baseUrl'], '/');
      unset($config['baseUrl']);
    } else {
      throw new StoreException('No base URL specified');
    }

    // Get VFS object
    if( empty($config['adapter']) ) {
      throw new StoreException('Unspecified or unsupported VFS type');
    }
    $adapter = $config['adapter'];
    unset($config['adapter']);
    if( !empty($config['params']) ) {
      $params = $config['params'];
    } else {
      $params = array();
    }
    unset($config['params']);

    $this->_vfsConfig = array(
      'adapter' => $adapter,
      'params' => $params
    );

//      $vfs = VfsFactory::factory($this->_vfsConfig['adapter'], $this->_vfsConfig['params']);

      parent::__construct($config);
  }

  public function getType()
  {
    return $this->_type;
  }

  public function getVfs()
  {
    if( null === $this->_vfs ) {
      $this->_vfs = VfsFactory::factory($this->_vfsConfig['adapter'], $this->_vfsConfig['params']);

      if( !($this->_vfs instanceof AbstractVfsAdapter) ) {
        throw new StoreException('Unable to load VFS adapter');
      }
    }

    return $this->_vfs;
  }




  /**
   * Returns a url that allows for external access to the file. May point to some
   * adapter which then retrieves the file and outputs it, if desirable
   *
   * @param Storage_Model_DbRow_File The file for operation
   * @return string
   */
  public function map(StorageFiles $model)
  {
    return $this->_baseUrl . '/' . ltrim($model->getStoragePath(), '/');
  }

  /**
   * Stores a local file in the storage service
   *
   * @param Zend_Form_Element_File|array|string $file Temporary local file to store
   * @param array $params Contains iden
   * @return string Storage type specific path (internal use only)
   */
  public function store($model, $file)
  {
    $path = $this->getScheme()->generate($model->toArray());

    // Copy file
    try {
      $this->getVfs()->put($path, $file);
    } catch( \Exception $e ) {
      throw $e;
    }

    return $path;
  }

  /**
   * Returns the content of the file
   *
   * @param Storage_Model_DbRow_File $model The file for operation
   * @param array $params
   */
  public function read(StorageFiles $model)
  {
    return $this->getVfs()->getContents($model->storage_path);
  }

  /**
   * Creates a new file from data rather than an existing file
   *
   * @param Storage_Model_DbRow_File $model The file for operation
   * @param string $data
   */
  public function write(StorageFiles $model, $data)
  {
    $path = $this->getScheme()->generate($model->toArray());

    // Copy file
    try {
      $this->getVfs()->putContents($path, $data);
    } catch( \Exception $e ) {
      throw $e;
    }

    return $path;
  }

  /**
   * Removes the file
   *
   * @param Storage_Model_DbRow_File $model The file for operation
   */
  public function remove(StorageFiles $model)
  {
    if( !empty($model->storage_path) ) {
      $this->getVfs()->unlink($model->storage_path);
    }
  }

  /**
   * Creates a local temporary local copy of the file
   *
   * @param Storage_Model_DbRow_File $model The file for operation
   */
  public function temporary(StorageFiles $model)
  {
    $tmp_file = APPLICATION_PATH . '/public/temporary/' . basename($model['storage_path']);
    $this->getVfs()->get($tmp_file, $model->storage_path);
    @chmod($tmp_file, 0777);
    return $tmp_file;
  }

  public function removeFile($path)
  {
    $this->getVfs()->unlink($path);
  }
}