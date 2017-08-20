<?php

namespace AppBundle\Controller;

use Elastica\Query;
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

        $query = new Query();
        $query->addSort([
            'id' => [
                'order' => 'desc',
            ],
        ]);

        $texts = $finder->find($query);

        return $this->render(
            'default/index.html.twig',
            compact('texts')
        );
    }
}
