<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf4ac47f923b2d541ee32745dc59576de
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sterkh\\Activica\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sterkh\\Activica\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitf4ac47f923b2d541ee32745dc59576de::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf4ac47f923b2d541ee32745dc59576de::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf4ac47f923b2d541ee32745dc59576de::$classMap;

        }, null, ClassLoader::class);
    }
}