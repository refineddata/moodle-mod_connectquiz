@refined_training @connect @connect_quiz_new
  Feature: connect_quiz
    In order to view connect quiz
    As an admin
    I need to ad a connect quiz

  Background:
    Given I log in as "admin"
    Then I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Connect Quiz" to section "1"
    Then I press "Browse Adobe Connect"
    Then I follow "Shared-content"
    Then I follow "CarolineTest"
    Then I follow "p8mk6r5u48j"
    And I set the following fields to these values:
     | Standard Icons | Large |
     | Icon Position  | Left  |
    And I press "Save and return to course"
    Then I log out


  @javascript
  Scenario: Check if the Connect Quiz is added in the course
    Given I log in as "admin"
    And I am on homepage
    Then I follow "Automation Course"
    Then I should see "Caroline's Test Presentation"
    Then I log out


