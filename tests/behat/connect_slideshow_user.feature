@refined_training @connect @connect_slideshow_user
  Feature: connect_slideshow_user
    In order to add connect slideshow
    As an admin
    I need to test connect slideshow as a testuser

  Background:
    Given I log in as "admin"
    And I follow "Turn editing on"
    And I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Automation Course |
     | Course short name | QA               |
     | Course category   | Miscellaneous    |
     | Visible           | Show             |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Add an activity or resource"
    And I add a "Slideshow" to section "1"
    And I follow "Browse Adobe Connect"
    And I follow "Shared-content"
    And I follow "Slideshow Automation"
    And I set the following fields to these values:
     | Display on course page | 1 |
     | Standard Icons         | Medium |
     | Icon Position          | Center |
     | Grading Options        | Simple Grading |
     | Visible                | Show           |
     | Group mode             | No groups      |
     | Grouping               | None           |
     | Completion tracking    | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out
    Given I log in as "testuser"
    And I follow "Automation Course"
    And I follow "Slideshow Automation"
    And I log out
    Given I log in as "admin"
    And I follow "Automation Course"
    And I expand "Slideshow Automation" node
    And I follow "Grades"
    And I log out

  @javascript
  Scenario: Check if the Slideshow is added in the course
    Given I log in as "admin"
    And I follow "Automation Course"
    Then I should see "Slideshow Automation"
    And I log out

  Scenario: Log in as a test user and complete the activity
    Given I log in as "testuser"
    And I follow "Automation Course"
    And I follow "Slideshow Automation"
    And I log out

  Scenario: Log in as a admin to check the grades
    Given I log in as "admin"
    And I follow "Automation Course"
    And I expand "Slideshow Automation" node
    And I follow "Grades"
    And I log out

     