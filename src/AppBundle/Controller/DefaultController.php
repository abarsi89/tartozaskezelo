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
        $em     = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb ->select('sum(d.amount)')
            ->from('AppBundle:Debt', 'd');
            //->where('d.id=?1');

        $sum = $qb  ->getQuery()
                    ->getSingleScalarResult();
                    //->getOneOrNullResult();

        $tokenStorage = $this->get('security.token_storage');
        if ($tokenStorage->getToken() instanceof UsernamePasswordToken) {
            return $this->redirectToRoute('admin');
        }
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', ['sum' => $sum
        ]);
    }

    public function adminAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //return new Response('<html><body>Admin page!</body></html>');
        return $this->render('default/signed.html.twig', ['last_username' => $lastUsername], [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    public function listAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $qu = $em->createQueryBuilder();
        $qu ->select('sum(u.amount)')
            ->from('AppBundle:Debt', 'u');
            //->where('u.id = ?1');

        $udebt = $qu->getQuery()
                    ->getSingleScalarResult();
        //->getOneOrNullResult();


        $debts = $em->getRepository('AppBundle:Debt')->findAll();

        return $this->render('default/signed.html.twig',[
            'debts' => $debts,
            'udebt' => $udebt]);

    }
}
