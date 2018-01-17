<?php
namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use TYPO3\Flow\Annotations as Flow;
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
     * @param \TYPO3\Flow\Mvc\ActionRequest $actionRequest
     * @return void
     */
    public function updateCredentials(\TYPO3\Flow\Mvc\ActionRequest $actionRequest)
    {
        /** @var Simple $authentication */
        $authentication = $this->authenticationInterface;
        $attributes = $authentication->getAttributes();
        $authDataArray = $authentication->getAuthDataArray();

        if (is_array($authDataArray)) {
            /** @var \SAML2\XML\saml\NameID $nameId */
            $nameId = $authDataArray['saml:sp:NameID'];
            if (! empty($nameId)) {
                $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
                $this->credentials['username'] = $nameId->value;
                $this->credentials['attributes'] = $attributes;
            }
        }
    }
}
