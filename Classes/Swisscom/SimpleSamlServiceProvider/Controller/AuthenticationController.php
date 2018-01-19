<?php
namespace Swisscom\SimpleSamlServiceProvider\Controller;

/*
 * This file is part of the Swisscom.SimpleSamlServiceProvider package.
 */

use SimpleSAML\Auth\Simple;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use TYPO3\Flow\Security\Exception\AuthenticationRequiredException;


class AuthenticationController extends AbstractAuthenticationController
{

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Security\Context
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
     * @return void
     */
    public function authenticateAction()
    {
        /** @var Simple $authentication */
        $authentication = $this->authenticationInterface;
        $authentication->requireAuth();

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
