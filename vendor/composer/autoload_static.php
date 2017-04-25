<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit97c9390a521cc59cccf3b8d948154da0
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yajra\\' => 6,
        ),
        'S' => 
        array (
            'Symfony\\Component\\CssSelector\\' => 30,
        ),
        'P' => 
        array (
            'PhpParser\\' => 10,
        ),
        'B' => 
        array (
            'Box\\Spout\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yajra\\' => 
        array (
            0 => __DIR__ . '/..' . '/yajra/laravel-pdo-via-oci8/src',
        ),
        'Symfony\\Component\\CssSelector\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/css-selector',
        ),
        'PhpParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/php-parser/lib/PhpParser',
        ),
        'Box\\Spout\\' => 
        array (
            0 => __DIR__ . '/..' . '/box/spout/src/Spout',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Thrift' => 
            array (
                0 => __DIR__ . '/..' . '/plansys/thrift/lib',
            ),
        ),
        'I' => 
        array (
            'InlineStyle' => 
            array (
                0 => __DIR__ . '/..' . '/inlinestyle/inlinestyle',
            ),
        ),
    );

    public static $classMap = array (
        'EasyPeasyICS' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/EasyPeasyICS.php',
        'PHPMailer' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
        'PHPMailerOAuth' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauth.php',
        'PHPMailerOAuthGoogle' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmaileroauthgoogle.php',
        'POP3' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.pop3.php',
        'SMTP' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.smtp.php',
        'ntlm_sasl_client_class' => __DIR__ . '/..' . '/phpmailer/phpmailer/extras/ntlm_sasl_client.php',
        'phpmailerException' => __DIR__ . '/..' . '/phpmailer/phpmailer/class.phpmailer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit97c9390a521cc59cccf3b8d948154da0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit97c9390a521cc59cccf3b8d948154da0::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit97c9390a521cc59cccf3b8d948154da0::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit97c9390a521cc59cccf3b8d948154da0::$classMap;

        }, null, ClassLoader::class);
    }
}