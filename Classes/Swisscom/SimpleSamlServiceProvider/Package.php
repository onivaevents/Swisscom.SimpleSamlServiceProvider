<?php
namespace Swisscom\SimpleSamlServiceProvider;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use TYPO3\Flow\Package\Package as BasePackage;

class Package extends BasePackage
{
    /**
     * @param \TYPO3\Flow\Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap)
    {
        /* TODO: Make this work. Problem: Cannot create instance of SimpleSamlAuthentication with constructor args
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(\TYPO3\Flow\Security\Authentication\AuthenticationProviderManager::class, 'loggedOut',
            \Swisscom\SimpleSamlServiceProvider\Authentication\SimpleSamlAuthentication::class, 'logout', false
        );
        */
    }
}
