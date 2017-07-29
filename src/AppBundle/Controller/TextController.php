<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Slug;
use AppBundle\Entity\Text;
use AppBundle\Entity\User;
use Doctrine\ORM\NoResultException;
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
                    'slugBody' => $text->getCurrentSlug()->getBody(),
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
     * @Route("/{username}/{slugBody}/_edit")
     * @param Request $request
     * @param string $username
     * @param string $slugBody
     * @return Response
     */
    public function editAction(Request $request, string $username, string $slugBody)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $textRepository = $this->getDoctrine()->getRepository('AppBundle:Text');
        $slugRepository = $this->getDoctrine()->getRepository('AppBundle:Slug');

        $slug = $slugRepository->findSlugByUserAndSlugBody($user, $slugBody);

        if (!$slug) {
            throw $this->createNotFoundException();
        }

        $text = $textRepository->findOneBy([
            'currentSlug' => $slug,
        ]);

        if (!$text) {
            $text = $slug->getText();

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $username,
                    'slugBody' => $text->getCurrentSlug()->getBody(),
                ]
            );
        }

        $this->denyAccessUnlessGranted(
            'edit',
            $text
        );

        $form = $this
            ->createForm(
                'AppBundle\Form\Type\Text\TitleType',
                $text,
                [
                    'action' => $this->generateUrl(
                        'app_text_edit',
                        [
                            'username' => $this->getUser()->getUsername(),
                            'slugBody' => $slug->getBody(),
                        ]
                    ),
                    'attr' => [
                        'id' => 'form-edit-text-title'
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

            $text->setCurrentSlug($slug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slug);
            $entityManager->persist($text);
            $entityManager->flush();

            $filesystem = $this->get('oneup_flysystem.collections_filesystem');

            $oldFilename = $this->appendFileExtension($slugBody);
            $newFilename = $this->appendFileExtension($slug->getBody());

            $filesystem->rename(
                $this->getPath($username, $oldFilename),
                $this->getPath($username, $newFilename)
            );

            $versionControlSystem = $this->get('app.version_control_system');
            $versionControlSystem->commitNewFilename(
                $oldFilename,
                $newFilename
            );

            return $this->redirectToRoute(
                'app_text_edit',
                [
                    'username' => $this->getUser()->getUsername(),
                    'slugBody' => $slug->getBody(),
                ]
            );
        }

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
                'AppBundle\Form\Type\Text\NewType',
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

        $text->setCurrentSlug($slug);

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

        $directorySeparator = DIRECTORY_SEPARATOR;

        $filesystem
            ->write(
                "$username$directorySeparator$filename",
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
        return $this->appendFileExtension(
            $text->getCurrentSlug()->getBody()
        );
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

    /**
     * @param string $username
     * @param string $filename
     * @return string
     */
    private function getPath(string $username, string $filename): string
    {
        return join('', [
            $username,
            DIRECTORY_SEPARATOR,
            $filename
        ]);
    }

    /**
     * @param string $slugBody
     * @param string $extension
     * @return string
     */
    private function appendFileExtension(string $slugBody, string $extension = 'md'): string
    {
        return join('', [
            $slugBody,
            '.',
            $extension
        ]);
    }
}
