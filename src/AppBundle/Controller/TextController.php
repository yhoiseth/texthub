<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Text;
use AppBundle\Entity\User;
use Stringy\Stringy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
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
            /** @var Text $unsavedText */
            $unsavedText = $form->getData();

            /** @var User $user */
            $user = $this->getUser();

            $slug = $this->generateSlug($unsavedText, $user);

            $text = $this->saveTextInDatabase($unsavedText, $slug, $user);

            $username = $user->getUsername();
            $userName = $user->getName();
            $email = $user->getEmail();

            $filename = "$slug.md";

            $this->saveTextFile($username, $filename);

            $this->commitTextFile($username, $filename, $userName, $email);

            return $this->redirectToRoute(
                'app_text_edit',
                compact(
                    'username',
                    'slug'
                )
            );
        }

        return $this->render(
            'AppBundle:Text:new.html.twig',
            [
                'form' => $form
                    ->createView(),
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
    private function incrementSlugVersion(string $slug): string
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

    /**
     * @param Text $text
     * @param User $user
     * @return string
     */
    private function generateSlug(Text $text, User $user): string
    {
        $slugify = $this->get('slugify');
        $slug = $slugify->slugify($text->getTitle());

        $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');

        $textsWithSameSlug = $textRepository
            ->findBy([
                'slug' => $slug,
                'createdBy' => $user,
            ]);

        while (count($textsWithSameSlug) > 0) {
            $slug = $this->incrementSlugVersion($slug);

            $textsWithSameSlug = $textRepository
                ->findBy([
                    'slug' => $slug
                ]);
        }

        return $slug;
    }

    /**
     * @return Form
     */
    private function createNewTextForm(): Form
    {
        $form = $this
            ->createForm(
                'AppBundle\Form\Type\TextType',
                null,
                [
                    'action' => $this->generateUrl('app_text_new')
                ]
            );

        return $form;
    }

    /**
     * @param Text $text
     * @param string $slug
     * @param User $user
     * @return Text
     */
    private function saveTextInDatabase(Text $text, string $slug, User $user): Text
    {
        $text->setSlug($slug);
        $text->setCreatedBy($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($text);
        $entityManager->flush();

        return $text;
    }

    /**
     * @param string $username
     * @param string $filename
     */
    private function saveTextFile(string $username, string $filename): void
    {
        $filesystem = $this->get('oneup_flysystem.main_repositories_filesystem');

        $filesystem
            ->write(
                "$username/$filename",
                ''
            );
    }

    /**
     * @param string $username
     * @param string $filename
     * @param string $userName
     * @param string $email
     */
    private function commitTextFile(string $username, string $filename, string $userName, string $email): void
    {
        $mainRepositoriesDirectory = $this->getParameter('repositories_main_directory');

        $navigationCommand = "cd $mainRepositoriesDirectory/$username";
        $stageCommand = "git add $filename";
        $commitCommand = "git commit --author='$userName <$email>' -m 'Create text'";

        $completeCommand = "$navigationCommand && $stageCommand && $commitCommand";

        shell_exec($completeCommand);
    }
}
