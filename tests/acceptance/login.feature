@watch
Feature: Login
  In order to authenticate
  As a web visitor
  I need to login

  Background:
    Given I am on "/"
    When I click "Register"
    And I fill in "Name" with "William Braxton Irvine"
    And I fill in "Username" with "williambirvine"
    And I fill in "Email" with "williambirvine@williambirvine.com"
    And I fill in "Password" with "visualize"
    And I fill in "Repeat password" with "visualize"
    And I press ENTER
    And I visit "/logout"

  Scenario: Username and password
    When I visit "/"
    And I click "Log in"
    And I fill in "Email or username" with "williambirvine"
    And I fill in "Password" with "visualize"
    And I click the "Log in" button
    Then I should be logged in

  Scenario: Email and password
    When I visit "/"
    And I click "Log in"
    And I fill in "Email or username" with "williambirvine@williambirvine.com"
    And I fill in "Password" with "visualize"
    And I click the "Log in" button
    Then I should be logged in

  Scenario: Wrong password
    When I visit "/"
    And I click "Log in"
    And I fill in "Email or username" with "williambirvine@williambirvine.com"
    And I fill in "Password" with "this is wrong"
    And I click the "Log in" button
    Then I should not be logged in
