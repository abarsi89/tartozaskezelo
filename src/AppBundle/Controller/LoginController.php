<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    public function indexAction(Request $request)
    {
        $tokenStorage = $this->get('security.token_storage');
        if ($tokenStorage->getToken() instanceof UsernamePasswordToken) {
            return $this->redirectToRoute('admin');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function logoutAction(Request $request)
    {
        //$cookie = $this->setcookie(new Cookie("localhost", "", time() - 3600));
        //$response->headers->setCookie(new Cookie('blabla', 'true', time() + (3600 * 48), '/', null, false, false));
        //clearCookie()

        //$this->get('security.context')->setToken(null);
        //$this->get('request')->getSession()->invalidate();

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}