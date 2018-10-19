<?php
namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Object\DependencyInjection\DependencyProxy;
use TYPO3\Flow\Security\Authentication\Token\AbstractToken;


class SamlToken extends AbstractToken
{
    /**
     * @var \Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface
     * @Flow\Inject
     */
    protected $authenticationInterface;

    /**
     * The username/password credentials
     * @var array
     * @Flow\Transient
     */
    protected $credentials = array('username' => '', 'attributes' => array());

    /**
     * @Flow\InjectConfiguration(path="attributeKeys.username")
     * @var string
     */
    protected $usernameAttributeKey;

    /**
     * @var string
     * @Flow\InjectConfiguration(path="authTokenCookieName")
     */
    protected $authTokenCookieName;

    /**
     * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
     * @return void
     */
    public function updateCredentials(\TYPO3\Flow\Mvc\ActionRequest $actionRequest)
    {
        if ($this->authenticationInterface instanceof DependencyProxy) {
            $this->authenticationInterface->_activateDependency();
        }
        if ($this->authenticationInterface instanceof Simple) {
            $attributes = $this->authenticationInterface->getAttributes();
            $authDataArray = $this->authenticationInterface->getAuthDataArray();
            if (is_array($authDataArray)) {
                /** @var \SAML2\XML\saml\NameID $nameId */
                $nameId = $authDataArray['saml:sp:NameID'];
                if (!empty($nameId)) {
                    $this->credentials['username'] = $nameId->value;
                } elseif (!empty($this->usernameAttributeKey) && array_key_exists($this->usernameAttributeKey, $attributes)) {
                    $username = $attributes[$this->usernameAttributeKey];
                    // ADFS sends the claims back as array
                    $this->credentials['username'] = is_array($username) ? array_shift($username) : $username;
                }
                $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
                $this->credentials['attributes'] = $attributes;

                /* Workaround: Remove the SAML authentication token cookie. The cookies causes problem with CSRF
                protection whenever it gets renewed. The token cookie is only used to authenticate. From here on, the
                cookie is not used anymore. */
                setcookie($this->authTokenCookieName, '', time() - 3600, '/');
            }
        }
    }
}