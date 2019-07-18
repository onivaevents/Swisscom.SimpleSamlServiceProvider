<?php

namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Session\SessionInterface;

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
     * @var SessionInterface
     * @Flow\Inject
     */
    protected $session;

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
        $params = is_array($params) ? array_merge($this->logoutParams, $params) : $this->logoutParams;
        foreach ($this->authenticationManager->getTokens() as $token) {
            if ($token instanceof SamlToken) {
                /** Logout will redirect and not return to logout process. Therefore the session is destroyed here.
                 * @see \Neos\Flow\Security\Authentication\AuthenticationProviderManager::logout() */
                if ($this->session->isStarted()) {
                    $this->session->destroy('Logout through SimpleSamlAuthentication');
                }
                parent::logout($params);
                return;
            }
        }
    }
}
