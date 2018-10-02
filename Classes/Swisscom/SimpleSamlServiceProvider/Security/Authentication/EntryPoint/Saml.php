<?php
namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\EntryPoint;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use Swisscom\SimpleSamlServiceProvider\Exception;
use TYPO3\Flow\Http\Request;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Security\Authentication\EntryPoint\AbstractEntryPoint;
use TYPO3\Flow\Annotations as Flow;


class Saml extends AbstractEntryPoint
{

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @var \Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface
     * @Flow\Inject
     */
    protected $authenticationInterface;

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception
     */
    public function startAuthentication(Request $request, Response $response)
    {
        /** @var Simple $authentication */
        $authentication = $this->authenticationInterface;
        if ($authentication->isAuthenticated()) {
            $authentication->logout();
            // Should automatically be authenticated by the SamlProvider, but something went wrong.
            throw new Exception('User is authenticated by the identity provider, but not able to be authenticated by system.', 1516117713);
        } else {
            $authentication->requireAuth($this->settings['loginParams']);
        }
    }
}
