<?php

namespace CanalTP\SamEcoreApplicationManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CanalTPSamEcoreApplicationManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
