<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2bdc9577c8480b3a07c873c282c09b00
{
    public static $files = array (
        '6124b4c8570aa390c21fafd04a26c69f' => __DIR__ . '/..' . '/myclabs/deep-copy/src/DeepCopy/deep_copy.php',
        'db356362850385d08a5381de2638b5fd' => __DIR__ . '/..' . '/mpdf/mpdf/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Math\\' => 15,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'M' => 
        array (
            'Mpdf\\PsrLogAwareTrait\\' => 22,
            'Mpdf\\PsrHttpMessageShim\\' => 24,
            'Mpdf\\' => 5,
        ),
        'D' => 
        array (
            'DeepCopy\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/math/src/Math',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Mpdf\\PsrLogAwareTrait\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpdf/psr-log-aware-trait/src',
        ),
        'Mpdf\\PsrHttpMessageShim\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpdf/psr-http-message-shim/src',
        ),
        'Mpdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/mpdf/mpdf/src',
        ),
        'DeepCopy\\' => 
        array (
            0 => __DIR__ . '/..' . '/myclabs/deep-copy/src/DeepCopy',
        ),
    );

    public static $classMap = array (
        'AltoRouter' => __DIR__ . '/..' . '/altorouter/altorouter/AltoRouter.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2bdc9577c8480b3a07c873c282c09b00::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2bdc9577c8480b3a07c873c282c09b00::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2bdc9577c8480b3a07c873c282c09b00::$classMap;

        }, null, ClassLoader::class);
    }
}
