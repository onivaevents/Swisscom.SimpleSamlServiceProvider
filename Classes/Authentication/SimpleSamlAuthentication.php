<?php

namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Session\SessionManagerInterface;
use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class SimpleSamlAuthentication extends \SimpleSAML\Auth\Simple implements AuthenticationInterface
{

    /**
     * @Flow\InjectConfiguration(path="logoutParams")
     * @var array
     */
    protected $logoutParams;

    /**
     * @var SessionManagerInterface
     * @Flow\Inject
     */
    protected $sessionManager;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Authentication\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param null $params
     */
    public function logout($params = null)
    {
        // TODO: Adapt to \Neos\Flow\Security\Authentication\AuthenticationProviderManager::logout() or even call the method directly if possible
        $session = $this->sessionManager->getCurrentSession();
        $params = is_array($params) ? array_merge($this->logoutParams, $params) : $this->logoutParams;
        foreach ($this->authenticationManager->getTokens() as $token) {
            if ($token instanceof SamlToken) {
                /** Logout will redirect and not return to logout process. Therefore the session is destroyed here.
                 * @see \Neos\Flow\Security\Authentication\AuthenticationProviderManager::logout() */
                if ($session->isStarted()) {
                    $session->destroy('Logout through SimpleSamlAuthentication');
                }
                parent::logout($params);
                return;
            }
        }
    }
}
