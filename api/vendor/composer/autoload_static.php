<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf0e191bdd5b6f7b4161a8ab0dc34c33f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'A' => 
        array (
            'Api\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf0e191bdd5b6f7b4161a8ab0dc34c33f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf0e191bdd5b6f7b4161a8ab0dc34c33f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf0e191bdd5b6f7b4161a8ab0dc34c33f::$classMap;

        }, null, ClassLoader::class);
    }
}
