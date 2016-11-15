@refined_training @connect @connect_activity_quiz_user
  Feature: connect_activity_quiz_user
    In order to achieve the grading for quiz activity
    As an admin
    I need to add connect quiz and set the grading

  Background:
    Given I log in as "admin"
    Then I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Connect quiz" to section "1"
    And I follow "Browse Adobe Connect"
    And I follow "Shared-content"
    And I follow "CarolineTest"
    And I follow "testq"
    And I follow "Expand all"
    And I set the following fields to these values:
     | Display on course page | 1 |
     | Standard Icons | Large |
     | Icon position | Left |
     | Grading Options | Simple Grading |
     | Visible | Show |
     | Group mode | No groups |
     | Grouping | None |
     | Completion tracking | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out
    Given I log in as "testuser"
    Then I follow "Automation Course"
    Then I follow "CarolineTest"
    And I click on "True" "button"
    And I press "Submit"
    And I press "Submit"
    And I click on "None of the above" "button"
    And I press "Submit"
    And I press "Submit"
    And I log out
    Given I log in as "admin"
    Then I follow "Automation Course"
    And I expand "Course administartion" node
    And I follow "Grades"
    And I log out


  @javascript:
  Scenario: Check if the connect activity quiz is added in the course
    Given I log in as "admin"
    Then I follow "Automation Course"
    Then I follow "Connect quiz"
    And I log out
    Given I log in as "testuser"
    Then I follow "Automation Course"
    Then I follow "Connect quiz"
    And I log out

  Scenario: Log in as test user to complete the connect quiz activity
    Given I log in as "testuser"
    Then I follow "Automation Course"
    Then I follow "Caroline Test"
    And I click on "True" "button"
    And I press "Submit"
    And I press "Submit"
    And I click on "None of the above" "button"
    And I press "Submit"
    And I press "Submit"
    And I log out

  Scenario: Log in as admin to check if the grades are reported
    Given I log in as "admin"
    Then I follow "Automation Course"
    Then I expand "Course administration" node
    Then I follow "Grades"
    And I log out



