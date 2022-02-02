<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Composer;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */
use Neos\Utility\Files;

/**
 * Composer install scripts to setup simplesamlphp
 */
class InstallerScripts
{
    public static function postUpdateAndInstall(): void
    {
        self::setupSimpleSamlPhpApp();
        self::setupSimpleSamlPhpConfig();
    }

    /**
     * Set symlink to the simplesamlphp's www directory
     * Patch the Web/.htaccess file with rewrite rule and config env variable
     */
    protected static function setupSimpleSamlPhpApp(): void
    {
        $symlink = 'Web/simplesaml';
        if (file_exists($symlink)) {
            return;
        }

        try {
            Files::createRelativeSymlink('Packages/Libraries/simplesamlphp/simplesamlphp/www', $symlink);
        } catch (\ErrorException $e) {
            echo 'SimpleSamlPhp app setup: Create symlink failed!' . PHP_EOL;
        }

        $original = 'Web/.htaccess';
        $patch = 'Packages/Application/Swisscom.SimpleSamlServiceProvider/Resources/Private/Scripts/htaccess.patch';
        exec(sprintf("patch %s < %s", $original, $patch));
        if (file_exists($original . '.rej')) {
            Files::unlink($original . '.rej');
            echo 'SimpleSamlPhp app setup: Patch for .htaccess failed!' . PHP_EOL;
        }
        echo 'SimpleSamlPhp app setup completed' . PHP_EOL;
    }

    /**
     * Create the config file structure
     */
    protected static function setupSimpleSamlPhpConfig(): void
    {
        $configurationDirectory = 'Configuration/SimpleSamlPhp';
        if (is_dir($configurationDirectory)) {
            return;
        }

        Files::createDirectoryRecursively($configurationDirectory . '/config');
        Files::createDirectoryRecursively($configurationDirectory . '/metadata');

        $source = 'Packages/Application/Swisscom.SimpleSamlServiceProvider/Resources/Private/Scripts/config-templates';
        $destination = $configurationDirectory . '/config';
        Files::copyDirectoryRecursively($source, $destination);

        echo 'SimpleSamlPhp configuration setup completed' . PHP_EOL;
    }
}
