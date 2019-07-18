<?php
namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class AuthenticationFactory
{
    /**
     * @param string $authenticationObjectClassName
     * @param string $authSource
     * @return AuthenticationInterface
     */
    public function create($authenticationObjectClassName, $authSource)
    {
        try {
            $authenticationInterface = new $authenticationObjectClassName($authSource);
        } catch (\SimpleSAML\Error\CriticalConfigurationError $e) {
            /* Prevent the application from completely failing if there is a wrong or in-existing config.
            The config path is set as environment variable via SetEnv(). */
            $authenticationInterface = null;
        }
        return $authenticationInterface;
    }
}
