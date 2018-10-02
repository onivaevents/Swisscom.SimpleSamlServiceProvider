<?php

namespace Swisscom\SimpleSamlServiceProvider\Authentication;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use TYPO3\Flow\Annotations as Flow;

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
     * @Flow\Inject
     * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
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
                parent::logout($params);
                return;
            }
        }
    }
}
