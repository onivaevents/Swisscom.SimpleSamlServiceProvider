<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Provider;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Persistence\PersistenceManagerInterface;
use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Authentication\Provider\AbstractProvider;
use Neos\Flow\Security\Authentication\TokenInterface;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Exception\UnsupportedAuthenticationTokenException;

class SamlProvider extends AbstractProvider
{
    /**
     * @Flow\Inject
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\InjectConfiguration(path="authTokenCookieName")
     * @var string
     */
    protected $authTokenCookieName;

    /**
     * Returns the class names of the tokens this provider can authenticate.
     *
     * @return string[]
     */
    public function getTokenClassNames(): array
    {
        return [SamlToken::class];
    }

    /**
     * @param TokenInterface $authenticationToken
     * @throws UnsupportedAuthenticationTokenException
     */
    public function authenticate(TokenInterface $authenticationToken): void
    {
        if (!($authenticationToken instanceof SamlToken)) {
            throw new UnsupportedAuthenticationTokenException(
                'This provider cannot authenticate the given token.',
                1516021100
            );
        }

        $account = null;
        $credentials = $authenticationToken->getCredentials();

        if (!is_array($credentials) || empty($credentials['username'])) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
        }

        $providerName = $this->name;
        $accountRepository = $this->accountRepository;
        $this->securityContext->withoutAuthorizationChecks(
            function () use ($credentials, $providerName, $accountRepository, &$account) {
                $account = $accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName(
                    $credentials['username'],
                    $providerName
                );
            }
        );

        if ($account === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
        } else {
            $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
            $account->authenticationAttempted(TokenInterface::AUTHENTICATION_SUCCESSFUL);
            $authenticationToken->setAccount($account);
            $this->accountRepository->update($account);
            $this->persistenceManager->whitelistObject($account);

            /* Workaround: Remove the SAML authentication token cookie. The cookies cause problems with CSRF
            protection whenever it gets renewed. The token cookie is only used to authenticate. From here on, the
            cookie is not used anymore. */
            setcookie($this->authTokenCookieName, '', time() - 3600, '/');
        }
    }

}
