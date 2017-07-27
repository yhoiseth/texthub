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
     * @return string
     * @internal param User $user
     */
    private function generateSlug(Text $text): string
    {
        $user = $this->getUser();

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
        /** @var User $user */
        $user = $this->getUser();
        $username = $user->getUsername();
        $userName = $user->getName();
        $email = $user->getEmail();
        $filename = $this->getTextFilename($text);

        $mainRepositoriesDirectory = $this->getParameter('collections_directory');

        $navigationCommand = "cd $mainRepositoriesDirectory/$username";
        $stageCommand = "git add $filename";
        $commitCommand = "git commit --author='$userName <$email>' -m 'Create text'";

        $completeCommand = "$navigationCommand && $stageCommand && $commitCommand";

        shell_exec($completeCommand);
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
}
