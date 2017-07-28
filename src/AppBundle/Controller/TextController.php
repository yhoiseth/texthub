<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Text;
use AppBundle\Entity\User;
use Stringy\Stringy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $form = $this->createNewTextForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Text $text */
            $text = $form->getData();

            $this->saveTextInDatabase($text);
            $this->saveEmptyTextFile($text);
            $this->commitTextFileToVersionControlSystem($text);

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $this->getUser()->getUsername(),
                    'slug' => $text->getSlug(),
                ]
            );
        }

        if ($form->isSubmitted()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'AppBundle:Text:new.html.twig',
            [
                'form' => $form->createView(),
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

        $form = $this
            ->createForm(
                'AppBundle\Form\Type\TextType',
                $text,
                [
                    'action' => $this->generateUrl(
                        'app_text_edit',
                        [
                            'username' => $this->getUser()->getUsername(),
                            'slug' => $text->getSlug(),
                        ]
                    ),
                    'attr' => [
                        'id' => 'form-edit-text'
                    ]
                ]
            )
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $text->setTitle($form->getData()->getTitle());

            $text->setSlug(
                $this->generateSlug($text)
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($text);
            $entityManager->flush();

            $filesystem = $this->get('oneup_flysystem.collections_filesystem');
            $filesystem->rename(
                $this->getUser()->getUsername().'/'.$slug.'.md',
                $this->getUser()->getUsername().'/'.$text->getSlug().'.md'
            );

            $versionControlSystem = $this->get('app.version_control_system');
            $versionControlSystem->commitNewFilename(
                "$slug.md",
                $text->getSlug().'.md'
            );

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $this->getUser()->getUsername(),
                    'slug' => $text->getSlug(),
                ]
            );
        }

        if ($form->isSubmitted()) {
            dump('form submitted');die;
        }

//        dump('form not submitted');die;

        $form->setData($text);

        return $this->render(
            '@App/Text/edit.html.twig',
            [
                'text' => $text,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return Form
     */
    private function createNewTextForm(): Form
    {
        $text = new Text();
        $text->setTitle('Untitled');

        $form = $this
            ->createForm(
                'AppBundle\Form\Type\TextType',
                $text,
                [
                    'action' => $this->generateUrl('app_text_new')
                ]
            );

        return $form;
    }

    /**
     * @param Text $text
     */
    private function saveTextInDatabase(Text &$text)
    {
        $text->setCreatedBy(
            $this->getUser()
        );

        $text->setSlug(
            $this->generateSlug($text)
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($text);
        $entityManager->flush();
    }

    /**
     * @param Text $text
     */
    private function saveEmptyTextFile(Text $text): void
    {
        /** @var User $user */
        $user = $this->getUser();
        $username = $user->getUsername();

        $filename = $this->getTextFilename($text);

        $filesystem = $this->get('oneup_flysystem.collections_filesystem');

        $filesystem
            ->write(
                "$username/$filename",
                ''
            );
    }

    /**
     * @param Text $text
     * @internal param string $username
     * @internal param string $filename
     * @internal param string $userName
     * @internal param string $email
     */
    private function commitTextFileToVersionControlSystem(Text $text): void
    {
        $versionControlSystem = $this->get('app.version_control_system');
        $filename = $this->getTextFilename($text);
        $message = 'Create text';

        $versionControlSystem->commit(
            $filename,
            $message
        );
    }

    /**
     * @param Text $text
     * @return string
     * @internal param $slug
     */
    private function getTextFilename(Text $text): string
    {
        $slug = $text->getSlug();
        $filename = "$slug.md";

        return $filename;
    }

    /**
     * @param Text $text
     * @return string
     * @internal param User $user
     */
    private function generateSlug(Text $text): string
    {
        $slugify = $this->get('slugify');
        $slug = $slugify->slugify($text->getTitle());

        while ($this->slugIsUnavailable($slug)) {
            $this->incrementSlugVersion($slug);
        }

        return $slug;
    }

    /**
     * @param string &$slug
     * @return void
     */
    private function incrementSlugVersion(string &$slug): void
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
        } else {
            $slug.= '-2';
        }
    }

    /**
     * @param string $slug
     * @return bool
     */
    private function slugIsUnavailable(string $slug): bool
    {
        $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');

        $textsWithSameSlug = $textRepository
            ->findBy([
                'slug' => $slug,
                'createdBy' => $this->getUser(),
            ])
        ;

        return count($textsWithSameSlug) > 0;
    }
}
