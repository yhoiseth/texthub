<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Elastica\Query\MatchPhrasePrefix;
use Elastica\Query\Nested;
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
                    'autocomplete' => 'off',
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

        $matchTitle = new MatchPhrasePrefix();
        $matchTitle->setFieldQuery('title', $searchingFor);

        $searchQuery = $queryBuilder->query()->bool()
            ->addShould(
                $matchTitle
            )
            ->addShould(
                $this->matchUserName($queryBuilder, $searchingFor)
            )
            ->addShould(
                $queryBuilder->query()->match()
                    ->setField('htmlBody', $searchingFor)
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

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $searchingFor
     * @return Nested
     */
    private function matchUserName(QueryBuilder $queryBuilder, string $searchingFor)
    {
        $matchUserNamePrefix = new MatchPhrasePrefix();
        $matchUserNamePrefix->setFieldQuery('createdBy.name', $searchingFor);

        $matchUserName = $queryBuilder->query()->nested()
            ->setPath('createdBy')
            ->setQuery(
                $queryBuilder->query()->bool()
                    ->addShould(
                        $matchUserNamePrefix
                    )
                    ->addShould(
                        $queryBuilder->query()->match()
                            ->setFieldQuery('createdBy.name', $searchingFor)
                            ->setFieldFuzziness('createdBy.name', 5)
                    )
            )
        ;

        return $matchUserName;
    }
}
