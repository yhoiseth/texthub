<?php

use AppBundle\Entity\Text;
use AppBundle\Entity\User;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\UserBundle\Doctrine\UserManager;
use League\Flysystem\FilesystemInterface;
use function Stringy\create as stringy;
use Codeception\Util\Locator;


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @var array
     */
    private $examples = [];

    /**
     * @Given I am on :arg1
     * @Given I visit :arg1
     */
    public function iAmOn($arg1)
    {
        $this->amOnPage($arg1);
    }

    /**
     * @When I click :arg1
     */
    public function iClick($arg1)
    {
        $this->click($arg1);
    }

    /**
     * @When I press ENTER
     */
    public function iPressENTER()
    {
        $this->pressKey(
            'input[type="submit"]',
            WebDriverKeys::ENTER
        );
    }

    /**
     * @When I fill in :field with :value
     * @param string $field
     * @param string $value
     */
    public function iFillInWith(string $field, string $value)
    {
        $this->fillField($field, $value);
    }

    /**
     * @When I press the :arg1 button
     */
    public function iPressTheButton($arg1)
    {
        $this->click("input[value=$arg1]");
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        $this->cantSee('Log in');
        $this->cantSee('Register');
    }

    /**
     * @Then I should be redirected to :arg1
     */
    public function iShouldBeRedirectedTo($arg1)
    {
        $this->seeCurrentUrlEquals($arg1);
    }

    /**
     * @Then I should have a Git repository
     */
    public function iShouldHaveAGitRepository()
    {
        /** @var UserManager $userManager */
        $userManager = $this->grabService('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->findUsers()[0];

        $username = $user->getUsernameCanonical();

        /** @var AppKernel $kernel */
        $kernel = $this->grabService('kernel');

        $projectDirectory = $kernel->getProjectDir();

        $collectionsDirectory = $projectDirectory.'/var/collections';

        verify(file_exists($collectionsDirectory))->true();

        $collectionDirectory = $collectionsDirectory.'/'.$username;

        verify(file_exists($collectionDirectory))->true();

        $gitDirectory = $collectionDirectory.'/.git';

        verify(file_exists($gitDirectory))
            ->true();

        $namesOfStandardFilesInGitDirectory = [
            'HEAD',
            'config',
            'description',
            'hooks',
            'info',
            'objects',
            'refs',
        ];

        foreach ($namesOfStandardFilesInGitDirectory as $filename) {
            $file = $gitDirectory.DIRECTORY_SEPARATOR.$filename;

            verify(file_exists($file))->true();
        }
    }

    /**
     * @Then I should see :text
     * @param string $text
     */
    public function iShouldSee(string $text)
    {
        $this->canSee($text);
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        $this->canSee('Log in');
        $this->canSee('Register');
    }

    /**
     * @When I submit the form
     */
    public function iSubmitTheForm()
    {
        $this->click('input[type=submit]');
    }

    /**
     * @Then I should not see :text
     * @param string $text
     */
    public function iShouldNotSee(string $text)
    {
        $this->cantSee($text);
    }

    /**
     * @Then the :label field should contain :value
     * @param string $label
     * @param string $value
     */
    public function theFieldShouldContain(string $label, string $value)
    {
        sleep(2);

        $this->canSeeInField($label, $value);
    }

    /**
     * @Then :string should be selected
     * @param string $string
     */
    public function shouldBeSelected(string $string)
    {
        sleep(2);
    }

    /**
     * @Then the text with title :textTitle should be created in the main repository of :username
     * @param string $textTitle
     * @param string $username
     */
    public function theTextShouldBeCreatedInTheMainRepositoryOf(string $textTitle, string $username)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->grabService('doctrine');
        $textRepository = $doctrine->getRepository('AppBundle:Text');

        $text = $textRepository->findOneBy([
            'title' => $textTitle
        ]);

        verify($text)->notNull();

        /** @var AppKernel $kernel */
        $kernel = $this->grabService('kernel');

        $projectDirectory = $kernel->getProjectDir();

        $slug = $text->getCurrentSlug()->getBody();

        verify_file(
            "$projectDirectory/var/collections/$username/$slug.md"
        )
            ->exists()
        ;
    }

    /**
     * @Then the page title should contain :string
     * @param string $string
     */
    public function thePageTitleShouldContain(string $string)
    {
        $this->canSeeInTitle($string);
    }

    /**
     * @Given I wait :numberOfSeconds seconds
     * @param string $numberOfSeconds
     */
    public function iWaitSeconds(string $numberOfSeconds)
    {
        sleep(
            (int) $numberOfSeconds
        );
    }

    /**
     * @Then all the files in the main repository of :username should be committed
     * @param string $username
     */
    public function allTheFilesInTheMainRepositoryOfShouldBeCommitted(string $username)
    {
        /** @var AppKernel $kernel */
        $kernel = $this->grabService('kernel');
        $projectDirectory = $kernel->getProjectDir();
        $mainRepository = "$projectDirectory/var/collections/$username";

        $navigationCommand = "cd $mainRepository";
        $statusCommand = "git status";
        $completeCommand = "$navigationCommand && $statusCommand";

        $output = shell_exec($completeCommand);

        verify($output)->contains('nothing to commit');
    }

    /**
     * @Then the last commit should be authored by :username
     * @param string $username
     * @internal param string $author
     */
    public function theLastCommitShouldBeAuthoredBy(string $username)
    {
        /** @var UserManager $userManager */
        $userManager = $this->grabService('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->findUserByUsername($username);
        $name = $user->getName();
        $email = $user->getEmail();
        $author = "$name <$email>";

        /** @var AppKernel $kernel */
        $kernel = $this->grabService('kernel');
        $projectDirectory = $kernel->getProjectDir();
        $mainRepository = "$projectDirectory/var/collections/$username";

        $navigationCommand = "cd $mainRepository";
        $logCommand = "git --no-pager log";
        $completeCommand = "$navigationCommand && $logCommand";

        $output = shell_exec($completeCommand);

        $lines = stringy($output)->lines();

        verify($lines[1])->equals("Author: $author");
    }

    /**
     * @Then the text file should be saved
     */
    public function theTextFileShouldBeSaved()
    {
        /** @var FilesystemInterface $filesystem */
        $filesystem = $this->grabService('oneup_flysystem.collections_filesystem');

        $fileContents = $filesystem->read('marcus-aurelius/meditations-revisited.md');

        verify_file($fileContents)
            ->contains(
                $this->getExample('textBody')
            )
        ;
    }

    /**
     * @Given :element should contain :text
     * @param string $element
     * @param string $text
     */
    public function shouldContain(string $element, string $text)
    {
        $this->see($text, $element);
    }

    /**
     * @Then the text title should be updated from :oldTitle to :newTitle
     * @param string $oldTitle
     * @param string $newTitle
     */
    public function theTextTitleShouldBeUpdatedFromTo(string $oldTitle, string $newTitle)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->grabService('doctrine');
        $textRepository = $doctrine->getRepository('AppBundle:Text');

        $oldText = $textRepository->findOneBy([
            'title' => $oldTitle
        ]);

        verify($oldText)->null();

        $newText = $textRepository->findOneBy([
            'title' => $newTitle
        ]);

        verify($newText)->isInstanceOf(Text::class);
    }

    /**
     * @Then the filename should be updated from :oldFilename to :newFilename
     * @param string $oldFilename
     * @param string $newFilename
     */
    public function theFilenameShouldBeUpdatedTo(string $oldFilename, string $newFilename)
    {
        /** @var FilesystemInterface $filesystem */
        $filesystem = $this->grabService('oneup_flysystem.collections_filesystem');

        verify($filesystem->has("marcus-aurelius/$oldFilename"))->false();
        verify($filesystem->has("marcus-aurelius/$newFilename"))->true();
    }

    /**
     * @When I fill in the title field in the edit text form with :value
     * @param string $value
     */
    public function iFillInInTheTitleFieldInTheEditTextFormWith(string $value)
    {
        $this->fillField(
            ['css' => '#form-edit-text-title #appbundle_text_title'],
            $value
        );
    }

    /**
     * @Then I should be denied access
     */
    public function iShouldBeDeniedAccess()
    {
        $this->canSee('Access Denied');
    }

    /**
     * @When the title field in the edit title form should be selected
     */
    public function theTitleFieldInTheEditTitleFormShouldBeSelected()
    {
        $this->verifyThatElementHasFocus(
            '#appbundle_text_title'
        );

        $this->verifyThatTextIsSelected(
            'Meditations Revisited'
        );
    }

    /**
     * @Then the title field in the new text form should be selected
     */
    public function theTitleFieldInTheNewTextFormShouldBeSelected()
    {
        $this->verifyThatElementHasFocus(
            '#appbundle_text_new_title'
        );

        $this->verifyThatTextIsSelected(
            'Untitled'
        );
    }

    /**
     * @When I fill in the body field with :value
     * @param string $value
     */
    public function iFillInTheBodyFieldWith(string $value)
    {
        $this->fillField(
            '#form_body',
            $value
        );

        $this->addExample('textBody', $value);
    }

    /**
     * @Then I see :title before the other texts
     * @param string $title
     */
    public function iSeeBeforeTheOtherTexts(string $title)
    {
        $this->canSee($title, Locator::firstElement('h3'));
//        $this->canSee(Locator::firstElement('h3'), $title);
    }

    /**
     * @Given the users
     * @param TableNode $users
     */
    public function theUsers(TableNode $users)
    {
        /** @var string[] $user */
        foreach ($users->getHash() as $user) {
            /**
             * @var string $name
             * @var string $username
             */
            extract($user);

            $this->amOnPage('/register');

            $this->fillField(
                'Name',
                $name
            );

            $this->fillField(
                'Email',
                "$username@example.com"
            );

            $this->fillField(
                'Username',
                $username
            );

            $this->fillField(
                'Password',
                $username
            );

            $this->fillField(
                'Repeat password',
                $username
            );

            $this->submitForm(
                'form[name="fos_user_registration_form"]',
                []
            );

            $this->cantSee('Register');

            $this->amOnPage('/logout');
        }
    }

    /**
     * @Given the texts
     * @param TableNode $texts
     */
    public function theTexts(TableNode $texts)
    {
        /** @var string[] $text */
        foreach ($texts->getHash() as $text) {
            /**
             * @var string $username
             * @var string $title
             * @var string $body
             */
            extract($text);

            $this->amOnPage('/login');
            $this->fillField(
                'Email or username',
                $username
            );

            $this->fillField(
                'Password',
                $username
            );

            $this->submitForm(
                'form[action="/login_check"]',
                []
            );

            $this->click('New text');

            sleep(1);

            $this->fillField(
                'Title',
                $title
            );

            $this->click("Let's go!");

            $this->shouldContain(
                'h1',
                $title
            );

            $this->iFillInTheBodyFieldWith($body);

            sleep(3);

            $this->click('Save text');

            $this->amOnPage('/logout');
        }
    }

    /**
     * @Then the search field should have focus
     */
    public function theSearchFieldShouldHaveFocus()
    {
        $this->verifyThatElementHasFocus('#query');
    }

    /**
     * @param string $text
     */
    private function verifyThatTextIsSelected(string $text): void
    {
        verify(
            $this->executeJS(
                "return window.getSelection().toString() === '$text'"
            )
        )->true();
    }

    private function verifyThatElementHasFocus(string $selector): void
    {
        verify(
            $this->executeJS(
                "return $('$selector').is(':focus')"
            )
        )->true();
    }

    /**
     * @return array
     */
    private function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * @param array $examples
     * @return AcceptanceTester
     */
    private function setExamples(array $examples): AcceptanceTester
    {
        $this->examples = $examples;

        return $this;
    }

    private function addExample(string $key, $value = null): AcceptanceTester
    {
        $examples = $this->getExamples();

        $examples[$key] = $value;

        $this->setExamples($examples);

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getExample(string $key)
    {
        return $this->getExamples()[$key];
    }
}
