<?php

namespace Yoda\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends Controller
{
    /**
     *
     * @return [type] [description]
     * @Route("/register", name="user_register")
     * @Template()
     */
    public function registerAction(Request $request)
    {

        $defaultUser = new User();
        $defaultUser->setUsername('Leia');

        $form = $this->createForm(new RegisterFormType(), $defaultUser);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $form->getData();
            $user->setPassword(
                $this->encodePassword($user, $user->getPlainPassword())
                );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Welcome to the Death Star, have a magical day!');
            $this->authenticateUser($user);
            $url = $this->generateUrl('event');
            return $this->redirect($url);
        }

        return array('form' => $form->CreateView());
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
        ->getEncoder($user)
        ;
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
    }
}