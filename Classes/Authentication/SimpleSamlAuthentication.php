<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Security\Authentication\AuthenticationManagerInterface;
use Neos\Flow\Security\Context;
use Neos\Flow\Session\SessionManagerInterface;
use SimpleSAML\Auth\Simple;
use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class SimpleSamlAuthentication extends Simple implements AuthenticationInterface
{

    /**
     * @Flow\InjectConfiguration(path="logoutParams")
     * @var array
     */
    protected $logoutParams;

    /**
     * @Flow\Inject
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @Flow\Inject
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @param string|array|null $params
     */
    public function logout($params = null)
    {
        // TODO: Adapt to \Neos\Flow\Security\Authentication\AuthenticationProviderManager::logout() or even call the method directly if possible
        $session = $this->sessionManager->getCurrentSession();
        $params = is_array($params) ? array_merge($this->logoutParams, $params) : $this->logoutParams;
        if ($this->securityContext->getAuthenticationTokensOfType(SamlToken::class)) {
            /** Logout will redirect and not return to logout process. Therefore the session is destroyed here.
             * @see \Neos\Flow\Security\Authentication\AuthenticationProviderManager::logout() */
            if ($session->isStarted()) {
                $session->destroy('Logout through SimpleSamlAuthentication');
            }
            parent::logout($params);
        }
    }
}
