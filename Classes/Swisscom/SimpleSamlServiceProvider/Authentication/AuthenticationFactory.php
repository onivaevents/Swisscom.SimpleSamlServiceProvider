<?php
namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use TYPO3\Flow\Annotations as Flow;

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
        $authenticationInterface = new $authenticationObjectClassName($authSource);
        return $authenticationInterface;
    }
}
