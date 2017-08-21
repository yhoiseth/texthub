<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $searchForm = $this
            ->createFormBuilder()
            ->add(
                'query',
                TextType::class, [
                    'label' => 'Search',
                    'attr' => [
                        'autofocus' => true,
                    ],
            ])
            ->getForm()
        ;

        $finder = $this->container->get('fos_elastica.finder.app.text');

        $query = new Query();
        $query->addSort([
            'id' => [
                'order' => 'desc',
            ],
        ]);

        $texts = $finder->find($query);

        $searchForm = $searchForm->createView();

        return $this->render(
            'default/index.html.twig',
            compact(
                'texts',
                'searchForm'
            )
        );
    }
}
