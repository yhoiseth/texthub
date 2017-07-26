<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Text;
use AppBundle\Entity\User;
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

            $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');

            $textsWithSameSlug = $textRepository
                ->findBy([
                    'slug' => $slug
                ])
            ;

            if (count($textsWithSameSlug) > 0) {
                $slug.= '-2';
            }







            $text->setSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($text);
            $entityManager->flush();

            /** @var User $user */
            $user = $this->getUser();
            $username = $user->getUsername();

            /** @var \AppKernel $kernel */
            $kernel = $this->get('kernel');
            $projectDirectory = $kernel->getProjectDir();

            touch("$projectDirectory/var/repositories/main/$username/$slug.md");

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $username,
                    'slug' => $slug,
                ]
            );
        }


        return $this->render(
            'AppBundle:Text:new.html.twig',
            [
                'form' => $form
                    ->createView()
            ]
        );
    }

    /**
     * @Route("/{username}/{slug}/_edit")
     * @param Request $request
     * @param string $username
     * @param string $slug
     * @return Response
     */
    public function editAction(Request $request, string $username, string $slug)
    {
        $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');

        $text = $textRepository->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render(
            '@App/Text/edit.html.twig',
            [
                'text' => $text,
            ]
        );
    }
}
