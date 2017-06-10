<?php


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
     */
    public function iAmOn($arg1)
    {
        $this->amOnPage('/');
    }

    /**
     * @When I click :arg1
     */
    public function iClick($arg1)
    {
        $this->click($arg1);
    }

    /**
     * @When I fill in the :arg1 field with :arg2
     */
    public function iFillInTheFieldWith($arg1, $arg2)
    {
        $this->fillField($arg1, $arg2);
    }

    /**
     * @When I press :arg1
     */
    public function iPress($arg1)
    {
        throw new \Codeception\Exception\Incomplete("Step `I press :arg1` is not defined");
    }

    /**
     * @Then my user account should be created
     */
    public function myUserAccountShouldBeCreated()
    {
        throw new \Codeception\Exception\Incomplete("Step `my user account should be created` is not defined");
    }

    /**
     * @Then I should have a Git repository
     */
    public function iShouldHaveAGitRepository()
    {
        throw new \Codeception\Exception\Incomplete("Step `I should have a Git repository` is not defined");
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        throw new \Codeception\Exception\Incomplete("Step `I should be logged in` is not defined");
    }

    /**
     * @Then I should be redirected to :arg1
     */
    public function iShouldBeRedirectedTo($arg1)
    {
        throw new \Codeception\Exception\Incomplete("Step `I should be redirected to :arg1` is not defined");
    }
}
