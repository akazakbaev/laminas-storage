<?php
/**
 * Created by PhpStorm.
 * User: akazakbaev@srs.lan
 * Date: 3/6/18
 * Time: 10:04 AM
 */
namespace Akazakbaev\LaminasStorage\Provider;

interface StorageInterface
{
    public function getIdentity();

    public function store($model, $file);
}