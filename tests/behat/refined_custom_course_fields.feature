@refined_training @connect @refined_custom_course_fields
  Feature: refined_custom_course_fields
    In order to set the custom course fields
    As an admin
    I need to navigate and set the custom course fields


  Background:
    Given I log in as "admin"
    Then I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Course Field Categories"
    And I follow "Add Category"
    And I set the following fields to these values:
     | Weight | 0.1 |
     | Name   | Course Field Category Automation |
    And I press "Save changes"
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Course fields"
    And I follow "Add Field"
    And I set the following fields to these values:
     | Category | Default Course Field Category |
     | Weight | 1 |
     | Short Name | Automation Field |
     | Name | Test Automation Field |
     | Data Type | Text             |
     | Size / Width | 50 |
     | Maxsize / Rows | 25 |
    And I press "Save changes"
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Course fields"
    And I follow "Add Field"
    And I set the following fields to these values:
     | Category | Course Field Category Automation |
     | Weight | 2 |
     | Short Name | Automation Field 2 |
     | Name | Test Automation Field 2 |
     | Data Type | Text |
     | Size / Width | 50 |
     | Maxsize / Rows | 25 |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Check if the Custom Course Fields are available in the course
    Given I log in as "admin"
    And I am on homepage
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Course Field Categories"
    Then I should see "Course Field Category Automation"
    And I log out

  Scenario: Check if the Course Fields are available in the settings
    Given I log in as "admin"
    And I am on homepage
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Course Fields"
    Then I should see "Test Automation Field"
    Then I should see "Test Automation Field 2"
    And I log out
