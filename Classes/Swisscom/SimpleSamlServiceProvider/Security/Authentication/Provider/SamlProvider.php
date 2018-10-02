<?php
namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\Provider;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Swisscom\SimpleSamlServiceProvider\Security\Authentication\Token\SamlToken;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Flow\Security\Authentication\Provider\AbstractProvider;
use TYPO3\Flow\Security\Authentication\TokenInterface;
use TYPO3\Flow\Security\Context;
use TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException;


class SamlProvider extends AbstractProvider
{
    /**
     * @var AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @var Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * Returns the class names of the tokens this provider can authenticate.
     *
     * @return array
     */
    public function getTokenClassNames()
    {
        return array(SamlToken::class);
    }

    /**
     * @param TokenInterface $authenticationToken The token to be authenticated
     * @return void
     * @throws UnsupportedAuthenticationTokenException
     */
    public function authenticate(TokenInterface $authenticationToken)
    {
        if (!($authenticationToken instanceof SamlToken)) {
            throw new UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1516021100);
        }

        /** @var $account Account */
        $account = null;
        $credentials = $authenticationToken->getCredentials();

        if (!is_array($credentials) || empty($credentials['username'])) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
        }

        $providerName = $this->name;
        $accountRepository = $this->accountRepository;
        $this->securityContext->withoutAuthorizationChecks(function () use ($credentials, $providerName, $accountRepository, &$account) {
            $account = $accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($credentials['username'], $providerName);
        });

        if ($account === null) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
        } else {
            $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
            $account->authenticationAttempted(TokenInterface::AUTHENTICATION_SUCCESSFUL);
            $authenticationToken->setAccount($account);
            $this->accountRepository->update($account);
            $this->persistenceManager->whitelistObject($account);
        }
    }

}
