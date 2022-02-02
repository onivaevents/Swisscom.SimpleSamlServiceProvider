<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Security\Authentication\EntryPoint;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleSAML\Auth\Simple;
use Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface;
use Swisscom\SimpleSamlServiceProvider\Exception;
use Neos\Flow\Security\Authentication\EntryPoint\AbstractEntryPoint;
use Neos\Flow\Annotations as Flow;

class Saml extends AbstractEntryPoint
{

    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @Flow\Inject
     * @var AuthenticationInterface
     */
    protected $authenticationInterface;

    public function startAuthentication(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var Simple|null $authentication */
        $authentication = $this->authenticationInterface;
        if ($authentication === null) {
            return $response;
        }

        if ($authentication->isAuthenticated()) {
            $authentication->logout();
            // Should automatically be authenticated by the SamlProvider, but something went wrong.
            throw new Exception(
                'User is authenticated by the identity provider, but not able to be authenticated by system.',
                1516117713
            );
        } else {
            $params = $this->settings['loginParams'];
            $options = $this->getOptions();
            if (isset($options['loginParams'])) {
                $params = array_merge($params, $options['loginParams']);
            }
            $authentication->requireAuth($params);
        }

        return $response;
    }
}
