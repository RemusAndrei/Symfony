<?php

// src/Yoda/EventBundle/DataFixtures/ORM/LoadEvents.php
namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsers implements FixtureInterface , ContainerAwareInterface
{
	/** 
	 * @var ContainerInterface
	 */	
	private $container;

	/**
	 * {@inheritDoc}
	 */
	
	public function load(ObjectManager $manager)
	{

		$user = new User();
		$user->setUsername('remus');
		$user->setEmail('remus@remus.com');
		$user->setPassword($this->encodePassword($user,'remuspass'));		
		$manager->persist($user);

		$admin = new User();
		$admin->setUsername('cicu');
		$admin->setEmail('cicu@cicu.com');
		$admin->setPassword($this->encodePassword($admin,'cicupass'));		
		$admin->setRoles(array('ROLE_ADMIN'));
		$manager->persist($admin);

// the queries aren't done until now
		$manager->flush();
	}

	public function encodePassword(User $user, $plainPassword)
	{
		$encoder = $this->container ->get('security.encoder_factory') ->getEncoder($user);

		return $encoder->encodePassword($plainPassword, $user->getSalt());
	}

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}