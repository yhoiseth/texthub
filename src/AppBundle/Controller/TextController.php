<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Text;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TextController extends Controller
{
    /**
     * @Route("/new")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $form = $this
            ->createForm(
                'AppBundle\Form\Type\TextType',
                null,
                [
                    'action' => $this->generateUrl('app_text_new')
                ]
            )
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Text $text */
            $text = $form->getData();

            $slugify = $this->get('slugify');
            $slug = $slugify->slugify($text->getTitle());
            $text->setSlug($slug);


            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($text);
            $entityManager->flush();

//            return $this->redirectToRoute();
        }


        return $this->render(
            'AppBundle:Text:new.html.twig',
            [
                'form' => $form
                    ->createView()
            ]
        );
    }

}
