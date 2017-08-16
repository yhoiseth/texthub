<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $finder = $this->container->get('fos_elastica.finder.app.text');

        $texts = $finder->find('');



        return $this->render(
            'default/index.html.twig',
            compact('texts')
        );
    }
}
