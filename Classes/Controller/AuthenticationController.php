<?php

declare(strict_types=1);

namespace Swisscom\SimpleSamlServiceProvider\Controller;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use Neos\Flow\Security\Context;
use SimpleSAML\Auth\Simple;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Flow\Security\Exception\AuthenticationRequiredException;
use Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface;

class AuthenticationController extends AbstractAuthenticationController
{

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var AuthenticationInterface
     */
    protected $authenticationInterface;

    /**
     * The login page
     */
    public function indexAction(): void
    {
        $this->view->assign('account', $this->securityContext->getAccount());
    }

    /**
     * @see \Swisscom\SimpleSamlServiceProvider\Security\Authentication\EntryPoint\Saml
     */
    public function authenticateAction(array $params = []): void
    {
        $params = array_merge($this->settings['loginParams'], $params);
        /** @var Simple $authentication */
        $authentication = $this->authenticationInterface;
        $authentication->requireAuth($params);

        parent::authenticateAction();
    }

    protected function onAuthenticationSuccess(?ActionRequest $originalRequest = null): string
    {
        if ($originalRequest instanceof ActionRequest) {
            $this->redirectToRequest($originalRequest);
        } else {
            $this->redirect('index');
        }

        return '';
    }

    protected function onAuthenticationFailure(?AuthenticationRequiredException $exception = null): void
    {
        parent::onAuthenticationFailure($exception);

        $this->redirect('index');
    }

    public function logoutAction(): void
    {
        parent::logoutAction();

        $this->redirect('index');
    }
}
