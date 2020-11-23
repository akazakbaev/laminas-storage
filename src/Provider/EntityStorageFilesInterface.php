<?php
namespace Akazakbaev\LaminasStorage\Provider;

interface EntityStorageFilesInterface
{
    public function getId();
    
    public function getParentFileId();
    
    public function getType();
    
    public function getStoragePath();
    
    public function getParentType();
    
    public function getParentId();
    
    public function getExtension();
    
    public function getName();
    
    public function getMimeMajor();
    
    public function getMimeMinor();
    
    public function getSize();
    
    public function getHash();
    
    public function getOwnerId();
    
    public function getOwnerType();
    
    public function getService();
    
    public function map();
    
    public function toJsonArray();
}