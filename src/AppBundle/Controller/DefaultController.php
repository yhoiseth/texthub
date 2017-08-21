<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $searchForm = $this
            ->createFormBuilder(
                null, [
                'csrf_protection' => false,
            ])
            ->add(
                'query',
                TextType::class, [
                    'label' => 'Search',
                    'attr' => [
                        'autofocus' => true,
                    ],
                    'block_name' => '',
            ])
            ->setMethod('GET')
            ->getForm()
        ;

        $finder = $this->container->get('fos_elastica.finder.app.text');

        $searchForm->handleRequest($request);

        $providedSearchTerm = $searchForm->getData()['query'];

        $query = new Query($providedSearchTerm);

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
