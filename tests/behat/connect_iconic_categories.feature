@refined_training @connect @connect_iconic_categories
  Feature: connect_iconic_categories
    In order to set iconic categories
    As an admin
    I need to add iconic categories block

  Background:
    Given I log in as "admin"
    And I am on homepage
    When I follow "Turn editing on"
    And I add the "Iconic Categories" block
    Then I should see "Iconic Categories"
    Then I follow "Edit Iconic Categories"
    And I follow "Add Category"
    And I set the following fields to these values:
     | Name | Test Category |
     | Category Description | Image Testing |
    And I press "Save changes"
    And I log out
    Given I log in as "admin"
    And I am on homepage
    When I follow "Turn editing on"
    Then I should see "Iconic Categories"
    Then I follow "Edit Iconic Categories"
    And I follow "Add Category"
    And I set the following fields to these values:
     | Name | Test Category Test |
     | Category Description | Image testing in course |
    And I press "Save changes"
    And I log out


  @javascript
  Scenario: The Iconic Categories block is shown
    Given I log in as "admin"
    When I am on homepage
    Then I should see "Iconic Categories"
    Then I log out

