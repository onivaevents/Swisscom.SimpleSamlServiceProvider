<?php
namespace Swisscom\SimpleSamlServiceProvider;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Package\Package as BasePackage;

class Package extends BasePackage
{
    /**
     * @param \Neos\Flow\Core\Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(\Neos\Flow\Core\Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(\Neos\Flow\Security\Authentication\AuthenticationProviderManager::class, 'loggedOut',
            \Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface::class, 'logout', false
        );
    }
}
