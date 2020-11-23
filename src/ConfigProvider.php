<?php

declare(strict_types=1);

namespace Akazakbaev\LaminasStorage;

use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class ConfigProvider
{

    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine' => $this->getDoctrineSettings(),
            'routes' => $this->getRoutes()
        ];
    }

    public function getDependencies() : array
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
            ],
            'aliases' => [
            ]
        ];
    }

    public function getRoutes()
    {
        return [

        ];
    }

    public function getDoctrineSettings()
    {
        return [
            'driver' => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                    'drivers' => [
                        __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                    ],
                ],
                __NAMESPACE__ . '_driver' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/../src/Entity']
                ],
            ]
        ];
    }


}
