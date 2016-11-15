@refined_training @connect @connect_restrict_access
  Feature: connect_restrict_access
    In order to add restrict access for a course
    As an admin
    I need to set restrict access

  Background:
    Given I log in as "admin"
    Then I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    Then I press "Generate"
    And I set the following fields to these values:
     | Activity Name | Automation Test Meeting-4 |
     | Duration      | 00:30                   |
     | Telephony     | None                    |
     | Meeting Template | **RDS Template C9 (rdstemplate) |
     | Display on course page | 1                         |
     | Standard Icons         | Medium                    |
     | Icon Position          | Center                    |
     | Grading Options        | Simple Grading            |
     | Completion Delay       | 01:00                     |
     | Unenrol after attendance | Never                   |
     | Issue certificate to Attendee | None               |
     | Visible                       | Show               |
     | Group mode                    | No groups          |
     | Grouping                      | None               |
     | Completion tracking           | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I follow "Home"
    And I expand "Site administration" node
    And I follow "Advanced features"
     | Enable conditional access | 1 |
    And I press "Save changes"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Activity modules" node
    And I follow "Certificate"
    And I set the following fields to these values:
     | Auto-issue upon completion | 1 |
    And I press "Save changes"
    And I follow "Home"
    And I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Certificate" to section "1"
    And I set the following fields to these values:
     | Certificate Name | Test Certificate |
     | Introduction     | Complete the activities in the course to achieve the certificate |
     | Auto-issue upon completion | Yes                                                    |
    Then I press "Add restrictions"
    Then I press "Grade"
    And I set the following fields to these values:
     | Grade | franklu |
     | 1     | 50      |
     | 1     | 100     |
     | Completion tracking | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out
    Given I log in as "testuser"
    And I follow "General"
    And I follow "Automation Course"
    And I follow "Automation Test Meeting"
    And I follow "Test Certificate"
    And I log out
    Given I log in as "admin"
    And I follow "General"
    And I follow "Automation Course"
    And I follow "Course 1"
    And I expand "Course administration" node
    And I follow "Grades"
    And I log out



  @javascript
  Scenario: Check if course is available in the course
    Given I log in as "admin"
    And I am on homepage
    And I follow "General"
    Then I follow "Automation Course"
    Then I log out

  Scenario: Log in as test user to complete the course
    Given I log in as "testuser"
    And I follow "General"
    And I follow "Automation Course"
    And I follow "Automation Test Meeting"
    And I follow "Test Certificate"
    And I log out

  Scenario: Log in as admin to check the grades
    Given I log in as "admin"
    And I am on homepage
    Then I follow "General"
    And I follow "Automation Course"
    And I expand "Course administration" node
    And I follow "Grades"
    And I log out



