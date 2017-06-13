<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {

        $tokenStorage = $this->get('security.token_storage');
        if ($tokenStorage->getToken() instanceof UsernamePasswordToken) {
            return $this->redirectToRoute('admin');
        }
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function adminAction()
    {
        //return new Response('<html><body>Admin page!</body></html>');
        return $this->render('default/signed.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function listAction(Request $request)
    {

        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Debt')->findAll();

        //$debts = $this->getDoctrine()->getRepository('AppBundle\Entity\User')->findAll();
        return $this->render('default/signed.html.twig',['debts' => $entity]);

    }
}
