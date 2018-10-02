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
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

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
        $params = array_merge($this->settings['logoutParams'], $params);
        foreach ($this->authenticationManager->getTokens() as $token) {
            if ($token instanceof SamlToken) {
                parent::logout($params);
                return;
            }
        }
    }
}
