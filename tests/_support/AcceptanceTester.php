<?php
use AppBundle\Entity\User;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\UserBundle\Doctrine\UserManager;


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
        $this->canSeeInCurrentUrl($arg1);
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

        $mainRepositoriesDirectory = $projectDirectory.'/var/repositories/main';

        verify(file_exists($mainRepositoriesDirectory))->true();

        $userMainRepositoryDirectory = $mainRepositoriesDirectory.'/'.$username;

        verify(file_exists($userMainRepositoryDirectory))->true();

        $userMainRepositoryGitDirectory = $userMainRepositoryDirectory.'/.git';

        verify(file_exists($userMainRepositoryGitDirectory))
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
            $file = $userMainRepositoryGitDirectory.DIRECTORY_SEPARATOR.$filename;

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
}
