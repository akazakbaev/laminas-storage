<?php
/**
 * Created by PhpStorm.
 * User: akazakbaev@srs.lan
 * Date: 5/26/18
 * Time: 7:18 PM
 */
namespace Akazakbaev\LaminasStorage\Scheme;

use Akazakbaev\LaminasStorage\Exception\StoreException;
use Akazakbaev\LaminasStorage\Provider\SchemeInterface;

class Dynamic implements SchemeInterface
{
    public function generate(array $params)
    {
        if( empty($params['parentType']) ) {
            throw new StoreException('Unspecified resource parent type');
        } else if( empty($params['parentId']) || !is_numeric($params['parentId']) ) {
            throw new StoreException('Unspecified resource identifier');
        } else if( empty($params['extension']) ) {
            throw new StoreException('Unspecified resource extension');
        }

        extract($params);

        $path = 'files' . '/';
        $path .= $parentType . '/';
        $path .= $parentId . '/';

        $path .= sprintf("%04x", $id)
            . '_' . substr($hash, 4, 4)
            . '.' . $extension;

        return $path;
    }
}