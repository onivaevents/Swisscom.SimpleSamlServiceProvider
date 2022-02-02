<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Flow\Security\Authentication\AuthenticationProviderManager;
use Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface;

class Package extends BasePackage
{
    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(
            AuthenticationProviderManager::class,
            'loggedOut',
            AuthenticationInterface::class,
            'logout',
            false
        );
    }
}
