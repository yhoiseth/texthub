<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Slug;
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
                    'slug' => $text->getLatestSlug()->getBody(),
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
        $slugRepository = $this->getDoctrine()->getRepository('AppBundle:Slug');

        $slug = $slugRepository->findOneBy([
            'body' => $slug,
        ]);

        $text = $textRepository->findOneBy([
            'latestSlug' => $slug,
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
                            'slug' => $text->getLatestSlug()->getBody(),
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

            $slug = new Slug();

            $slug->setText($text);

            $slug->setBody($this->generateSlugBody($text));

            $text->setLatestSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slug);
            $entityManager->persist($text);
            $entityManager->flush();

            $filesystem = $this->get('oneup_flysystem.collections_filesystem');
            $filesystem->rename(
                $this->getUser()->getUsername().'/'.$slug.'.md',
                $this->getUser()->getUsername().'/'.$text->getLatestSlug()->getBody().'.md'
            );

            $versionControlSystem = $this->get('app.version_control_system');
            $versionControlSystem->commitNewFilename(
                "$slug.md",
                $text->getLatestSlug()->getBody().'.md'
            );

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $this->getUser()->getUsername(),
                    'slug' => $text->getLatestSlug()->getBody(),
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

        $slug = new Slug();

        $slug->setText($text);

        $slug->setBody(
            $this->generateSlugBody($text)
        );

        $text->setLatestSlug($slug);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($text);
        $entityManager->persist($slug);
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
        $slug = $text->getLatestSlug()->getBody();
        $filename = "$slug.md";

        return $filename;
    }

    /**
     * @param Text $text
     * @return string
     * @internal param User $user
     */
    private function generateSlugBody(Text $text): string
    {
        $slugify = $this->get('slugify');
        $slugBody = $slugify->slugify($text->getTitle());

        while ($this->slugBodyIsUnavailable($slugBody)) {
            $this->incrementSlugBodyVersion($slugBody);
        }

        return $slugBody;
    }

    /**
     * @param string &$slugBody
     * @return void
     */
    private function incrementSlugBodyVersion(string &$slugBody): void
    {
        /** @var Stringy[] $parts */
        $parts = stringy($slugBody)->split('-');

        $numberOfParts = count($parts);
        $lastPartIndex = $numberOfParts - 1;
        $lastPart = $parts[$lastPartIndex];

        if (ctype_digit((string) $lastPart)) {
            $oldVersionNumber = (integer) (string) $lastPart;
            $incrementedVersionNumber = $oldVersionNumber + 1;

            $newLastPart = stringy((string) $incrementedVersionNumber);
            $parts[$lastPartIndex] = $newLastPart;

            $slugBody = implode('-', $parts);
        } else {
            $slugBody.= '-2';
        }
    }

    /**
     * @param string $slugBody
     * @return bool
     */
    private function slugBodyIsUnavailable(string $slugBody): bool
    {
        $slugRepository = $this->getDoctrine()->getRepository('AppBundle:Slug');

        $queryForSlugsWithSameBodyBySameUser = $slugRepository->createQueryBuilder('slug')
            ->where('slug.body = :slugBody')
            ->setParameter('slugBody', $slugBody)
            ->join('slug.text', 'text')
            ->andWhere('text.createdBy = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
        ;

        return count($queryForSlugsWithSameBodyBySameUser->getResult()) > 0;
    }
}
