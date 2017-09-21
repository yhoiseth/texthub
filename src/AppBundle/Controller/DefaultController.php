<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Elastica\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $searchForm = $this->get('form.factory')
            ->createNamedBuilder(
                '',
                'Symfony\Component\Form\Extension\Core\Type\FormType',
                null, [
                    'csrf_protection' => false,
                ]
            )
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
            ->setAction(
                $this->generateUrl(
                    'app_text_search'
                )
            )
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

    /**
     * @param Request $request
     * @Route("/_search", name="app_text_search")
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $searchingFor = $request->query->get('query');
        $queryBuilder = new QueryBuilder();

        $matchPhrasePrefixQuery = new Query\MatchPhrasePrefix();
        $matchPhrasePrefixQuery->setFieldQuery('title', $searchingFor);

        $searchQuery = $queryBuilder->query()->bool()
            ->addMust(
                $matchPhrasePrefixQuery
            )
        ;

        $finder = $this->get('fos_elastica.finder.app.text');

        $texts = $finder->find($searchQuery);

        return $this->render(
            ':Text:list.html.twig',
            compact(
                'texts'
            )
        );
    }
}
