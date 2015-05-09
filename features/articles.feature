# language: en
Feature: 
  In order to tell the masses what's on my mind
  As a user
  I want to read articles on the site

  Background:
    Given there is a post:
      | Title              | Body                          |
      | The title          | This is the post body.        | 
      | A title once again | And the post body follows.    |
      | Title strikes back | This is really exciting! Not. |
    And there is a user:
      | Username | Password | FirstName | LastName |
      | alice    | ecila    | Alice     | Smith    |
      | bob      | obo      | Bob       | Johnson  |
    And there is a category:
      | Name      |
      | Events    |
      | Computers |
      | Foods     |

  Scenario: Show articles
    When I am on "TopPage"
    Then I should see "The title"
    And  I should see "A title once again"
    And  I should see "Title strikes back"

  Scenario: Show the article
    Given I am on "TopPage"
    When  I follow "A title once again"
    Then  I should see "And the post body follows."

  Scenario: Add new article
    Given I am on "TopPage"
    And   I follow "Add"
    And   I login "bob" "obo"
    When  I post article form :
      | Label      | Value                 |
      | Categories | Events                |
      | Title      | Today is Party        |
      | Body       | From 19:30 with Alice |
    And   I should see "Your article has been saved."
    And   I should see "Today is party"

  Scenario: Remove article
    Given I am on "TopPage"
    When  I delete article "Title strikes back"
    Then  I should not see "Title strikes back"
