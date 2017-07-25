<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class TextController extends Controller
{
    /**
     * @Route("/new")
     */
    public function newAction()
    {
        return $this->render(
            'AppBundle:Text:new.html.twig',
            [
                'form' => $this->createForm('AppBundle\Form\Type\TextType')->createView()
            ]
        );
    }

}
