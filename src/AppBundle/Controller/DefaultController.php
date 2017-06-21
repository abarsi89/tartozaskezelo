<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Debt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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


        $userRepository = $em->getRepository("AppBundle:User");
        $user = $userRepository->findOneBy(["username" => "jeges"]);
        // Add the role that you want !
        $user->addRole("ROLE_ADMIN");
        // Save changes in the database
        $em->persist($user);
        $em->flush();


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


        $user = $this->getUser();
        $actualUsername = $user->getUsername();

        if($actualUsername === "jeges")
        {
            $em     = $this->getDoctrine()->getManager();
            $qu = $em->createQueryBuilder();
            $qu ->select('sum(u.amount)')
                ->from('AppBundle:Debt', 'u');
            //->where('u.id = ?1');

            $adebt = $qu->getQuery()
                ->getSingleScalarResult();
            //->getOneOrNullResult();


            $debts = $em->getRepository('AppBundle:Debt')->findAll();

            return $this->render('default/admin.html.twig',[
                'debts' => $debts,
                'adebt' => $adebt]);

        } else {
            // Lekérem az összes tartozást ami a rendszerben van és összesítem
            $em     = $this->getDoctrine()->getManager();

            $qu = $em->createQueryBuilder();
            $qu ->select('sum(u.amount)')
                ->from('AppBundle:Debt', 'u')
                ->where('u.debtor = :actualUsername')
                ->setParameter('actualUsername', $actualUsername);

            $qe = $em->createQueryBuilder();
            $qe ->select('sum(u.amount)')
                ->from('AppBundle:Debt', 'u')
                ->where('u.creditor = :actualUsername')
                ->setParameter('actualUsername', $actualUsername);

            $udebt = $qu->getQuery()->getSingleScalarResult();
            $edebt = $qe->getQuery()->getSingleScalarResult();

            $balance = $edebt - $udebt;

            // Kigyűjtöm azokat a rekordokat ahol a felhasználó neve megegyezik az adósokkal
            $s1 = $em->createQueryBuilder();
            $s1 ->select('u.id, u.creditor, u.amount, u.created')
                 ->from('AppBundle:Debt', 'u')
                ->where('u.debtor = :actualUsername')
                ->setParameter('actualUsername', $actualUsername);

            $sql1 = $s1->getQuery()->execute();
            //$sql1 = $em->getRepository('AppBundle:Debt')->findAll();

            // Kigyűjtöm azokat a rekordokat ahol a felhasználó neve megegyezik a hitelezőkkel
            $s2 = $em->createQueryBuilder();
            $s2 ->select('u.id, u.debtor, u.amount, u.created')
                ->from('AppBundle:Debt', 'u')
                ->where('u.creditor = :actualUsername')
                ->setParameter('actualUsername', $actualUsername);


            $sql2 = $s2->getQuery()->execute();

            return $this->render('default/signed.html.twig', ['balance' => $balance, 'sql1' => $sql1, 'sql2' => $sql2]);
        }
    }

    public function newDebtAction(Request $request)
    {
        $rec = new Debt();

        /** @var Form $form */
        $form = $this->createFormBuilder($rec)
            ->add('debtor', TextType::class, array('label' => 'Adós'))
            ->add('creditor', TextType::class, array('label' => 'Hitelező'))
            ->add('amount', NumberType::class, array('label' => 'Összeg'))
            ->add('save', SubmitType::class, array('label' => 'Mentés'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Debt $data */
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($data);

            $em->flush();

            /*$qu = $em->createQueryBuilder();

            $qu ->insert('debt')
                ->setValue('debtor', '?')
                ->setValue('creditor', '?')
                ->setValue('amount', '?')
                ->setParameter(0, $data->getDebtor())
                ->setParameter(1, $data->getCreditor())
                ->setParameter(2, $data->getAmount());

            $qu->getQuery()->execute();*/

            // Oldal redirectálása
            $qu = $em->createQueryBuilder();
            $qu ->select('sum(u.amount)')
                ->from('AppBundle:Debt', 'u');

            $adebt = $qu->getQuery()
                ->getSingleScalarResult();

            $debts = $em->getRepository('AppBundle:Debt')->findAll();

            return $this->render('default/admin.html.twig',[
                'debts' => $debts,
                'adebt' => $adebt]);
        }


        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteDebtAction(Request $request)
    {
        // Sor törlése
        $record = $this->getDoctrine()
            ->getRepository('AppBundle:Debt')
            ->findOneById($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($record);
        $em->flush();

        // Oldal redirectálása
        return $this->redirectToRoute('admin');
    }

    public function completedAction(Request $request)
    {
        $user = $this->getUser();
        $actualUsername = $user->getUsername();

        $datas = $this->getDoctrine()
            ->getRepository('AppBundle:Debt')
            ->findOneById($request->get('id'));

        $paid = $datas->getPaid();
        $comp_name = $datas->getComp_name();

        if($actualUsername === "jeges")
        {
            // Teljesítés admin által
            /** @var Debt $record */
            $record = $this->getDoctrine()
                ->getRepository('AppBundle:Debt')
                ->findOneById($request->get('id'));

            $record->setPaid(2)->setComp_name($actualUsername);

            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();

            return $this->redirectToRoute('admin');

        } else {
            if('paid' == 0) {
                // Egyik oldal teljesítettnek jelöli a tartozást
                /** @var Debt $record */
                $record = $this->getDoctrine()
                    ->getRepository('AppBundle:Debt')
                    ->findOneById($request->get('id'));

                $record->setPaid(1)->setComp_name($actualUsername);

                $em = $this->getDoctrine()->getManager();
                $em->persist($record);
                $em->flush();

                /*$em = $this->getDoctrine()->getManager();

                $qu = $em->createQueryBuilder();
                $qu->update('AppBundle:Debt', 'd')
                    ->set('d.paid', 1)
                    ->where('d.id = :id')
                    ->setParameter('id', $record);

                $query = $qu->getQuery()->execute();*/

                return $this->redirectToRoute('admin');

            } elseif ('paid' == 1 && 'comp_name' != $actualUsername){
                // Másik oldal is teljesítettnek jelöli a tartozást
                /** @var Debt $record */
                $record = $this->getDoctrine()
                    ->getRepository('AppBundle:Debt')
                    ->findOneById($request->get('id'));

                $record->setPaid(2);

                $em = $this->getDoctrine()->getManager();
                $em->persist($record);
                $em->flush();
            }
        }
    }
}
