<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\ObjectManagement\DependencyInjection\DependencyProxy;
use Neos\Flow\Security\Authentication\Token\AbstractToken;
use SAML2\XML\saml\NameID;
use SimpleSAML\Auth\Simple;
use Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface;

class SamlToken extends AbstractToken
{
    /**
     * @Flow\Inject
     * @var AuthenticationInterface
     */
    protected $authenticationInterface;

    /**
     * @var array
     * @Flow\Transient
     */
    protected $credentials = ['username' => '', 'attributes' => []];

    /**
     * @Flow\InjectConfiguration(path="attributeKeys.username")
     * @var string
     */
    protected $usernameAttributeKey;

    public function updateCredentials(ActionRequest $actionRequest): void
    {
        if ($this->authenticationInterface instanceof DependencyProxy) {
            $this->authenticationInterface->_activateDependency();
        }
        if ($this->authenticationInterface instanceof Simple) {
            $attributes = $this->authenticationInterface->getAttributes();
            $authDataArray = $this->authenticationInterface->getAuthDataArray();

            if (is_array($authDataArray)) {
                // Special case: use username defined by attribute key setting
                if (!empty($this->usernameAttributeKey) && array_key_exists($this->usernameAttributeKey, $attributes)) {
                    $username = $attributes[$this->usernameAttributeKey];
                    // ADFS sends the claims back as array
                    $this->credentials['username'] = is_array($username) ? array_shift($username) : $username;

                // Default case: use SAML default nameId property
                } else {
                    /** @var NameID $nameId */
                    $nameId = $authDataArray['saml:sp:NameID'];
                    if (!empty($nameId)) {
                        $this->credentials['username'] = $nameId->getValue();
                    }
                }
                $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
                $this->credentials['attributes'] = $attributes;
            }
        }
    }
}
