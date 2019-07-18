<?php
namespace Swisscom\SimpleSamlServiceProvider\Controller;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Flow\Security\Exception\AuthenticationRequiredException;


class AuthenticationController extends AbstractAuthenticationController
{

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Context
     */
    protected $securityContext;

    /**
     * @var \Swisscom\SimpleSamlServiceProvider\Authentication\AuthenticationInterface
     * @Flow\Inject
     */
    protected $authenticationInterface;

    /**
     * The login page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('account', $this->securityContext->getAccount());
    }

    /**
     * @see \Swisscom\SimpleSamlServiceProvider\Security\Authentication\EntryPoint\Saml
     * @param array $params
     * @return void
     */
    public function authenticateAction($params = array())
    {
        $params = array_merge($this->settings['loginParams'], $params);
        /** @var Simple $authentication */
        $authentication = $this->authenticationInterface;
        $authentication->requireAuth($params);

        parent::authenticateAction();
    }

    /**
     * @param ActionRequest $originalRequest
     * @return void
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null)
    {
        if ($originalRequest instanceof ActionRequest) {
            $this->redirectToRequest($originalRequest);
        } else {
            $this->redirect('index');
        }
    }

    /**
     * @param AuthenticationRequiredException $exception
     * @return void
     */
    protected function onAuthenticationFailure(AuthenticationRequiredException $exception = null)
    {
        parent::onAuthenticationFailure($exception);

        $this->redirect('index');
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        parent::logoutAction();

        $this->redirect('index');
    }
}
