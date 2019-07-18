<?php
namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\DependencyInjection\DependencyProxy;
use Neos\Flow\Security\Authentication\Token\AbstractToken;


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
     * @param \Neos\Flow\Mvc\ActionRequest $actionRequest
     * @return void
     */
    public function updateCredentials(\Neos\Flow\Mvc\ActionRequest $actionRequest)
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
            }
        }
    }
}