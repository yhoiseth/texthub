<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Text;
use AppBundle\Entity\User;
use Stringy\Stringy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Stringy\create as stringy;

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

            /** @var User $user */
            $user = $this->getUser();

            $slugify = $this->get('slugify');
            $slug = $slugify->slugify($text->getTitle());

            $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');

            $textsWithSameSlug = $textRepository
                ->findBy([
                    'slug' => $slug,
                    'createdBy' => $user,
                ])
            ;

            while (count($textsWithSameSlug) > 0) {
                $slug = $this->incrementSlug($slug);

                $textsWithSameSlug = $textRepository
                    ->findBy([
                        'slug' => $slug
                    ])
                ;
            }

            $text->setSlug($slug);

            $text->setCreatedBy($user);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($text);
            $entityManager->flush();

            $username = $user->getUsername();
            $userName = $user->getName();
            $email = $user->getEmail();

            /** @var \AppKernel $kernel */
            $kernel = $this->get('kernel');
            $projectDirectory = $kernel->getProjectDir();
            $filename = "$slug.md";

            $filesystem = $this->get('oneup_flysystem.my_filesystem_filesystem');

//            dump($filesystem);

//            touch("$projectDirectory/var/repositories/main/$username/$filename");

            $result = $filesystem
                ->write(
                    "$username/$filename",
                    ''
                )
            ;
//
//            dump($result);die;

            $navigationCommand = "cd $projectDirectory/var/repositories/main/$username";
            $stageCommand = "git add $filename";
            $commitCommand = "git commit --author='$userName <$email>' -m 'Create text'";

            $completeCommand = "$navigationCommand && $stageCommand && $commitCommand";

            shell_exec($completeCommand);

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

    /**
     * @param string $slug
     * @return string
     */
    private function incrementSlug(string $slug): string
    {
        /** @var Stringy[] $parts */
        $parts = stringy($slug)->split('-');

        $numberOfParts = count($parts);
        $lastPartIndex = $numberOfParts - 1;
        $lastPart = $parts[$lastPartIndex];

        if (ctype_digit((string) $lastPart)) {
            $oldVersionNumber = (integer) (string) $lastPart;
            $incrementedVersionNumber = $oldVersionNumber + 1;

            $newLastPart = stringy((string) $incrementedVersionNumber);
            $parts[$lastPartIndex] = $newLastPart;

            $slug = implode('-', $parts);

            return $slug;
        }

        return $slug . '-2';
    }
}
