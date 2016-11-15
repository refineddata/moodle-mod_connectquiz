@refined_training @connect @connect_activity_quiz
  Feature: connect_activity_quiz
    In order to view connect activity quiz
    As an admin
    I need to add connect activity quiz

  Background:
    Given I log in as "admin"
    Then I follow "Turn editing on"
    And I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Automation Course |
     | Course short name | QA               |
     | Course category   | Miscellaneous    |
     | Visible           | Show             |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Automation Activity Quiz"
    And I set the following fields to these values:
     | Duration | 00:30 |
     | Telephony | None |
     | Meeting template | RDS Template C9 (rdstemplate) |
     | Display on course page | 1 |
     | Standard Icons | Medium |
     | Icon Position | Center |
     | Suppress all Icon text | 1 |
     | Grading options | Simple Grading |
     | Unenrol after attendance | Never |
     | Issue certificate to Attendance | None |
     | Visible | Show |
     | Group mode | No groups |
     | Grouping | None |
     | Completion tracking | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Connect Quiz" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Automation Activity Quiz"
    And I set the following fields to these values:
     | Duration | 00:45 |
     | Telephony | None |
     | Meeting template | RDS Template C9 (rdstemplete) |
     | Display on course page | 1                       |
     | Standard Icons         | Medium                  |
     | Icon Position          | Center                  |
     | Suppress all Icon text | 1                       |
     | Grading options        | Simple Grading          |
     | Unenrol after attendance | Never                 |
     | Issue certificate to Attendee | None             |
     | Visible                       | Show             |
     | Group mode                    | No groups        |
     | Grouping                      | None             |
     | Completion tracking           | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I log out

  @javascript
  Scenario: Check if connect activity is displayed
    Given I log in as "admin"
    And I am on homepage
    Then I follow "Automation Course"
    Then I follow "Automation Activity Quiz"
    Then I log out


