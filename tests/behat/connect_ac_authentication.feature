@refined_training @connect @connect_ac_authentication
  Feature: connect_ac_authentication
    In order to check AC authentication
    As an admin
    I need to create users in both LMS and AC


  Background:
    Given I log in as "admin"
    Then I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Course 1 |
     | Course short name | C1 |
     | Course category | Miscellaneous |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-Meetings"
    And I follow "Moodle Rooms"
    And I follow "Caroline"
    And I set the following fields to these values:
     | Display on course page | 1 |
     | Standard Icons         | Medium |
     | Icon Position          | Center |
     | Grading Options        | Simple Grading |
     | Completion Delay       | 01:00          |
     | Unenrol after attendance | Never        |
     | Issue certificate to Attendee | None    |
     | Visible                       | Show    |
     | Group mode                    | No groups |
     | Grouping                      | None      |
     | Available for group members only | 0      |
     | Completion tracking              | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Authentication" node
    And I follow "Adobe Connect"
    And I set the following fields to these values:
     | Add LMS Users | Yes |
     | Add Admins    | No  |
    And I press "Save changes"
    And I follow "Home"
    And I follow "Course 1"
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    Then I follow "Add a new user"
    And I set the following fields to these values:
     | Username | student1 |
     | New password | Abc@1234 |
     | First name   | Student  |
     | Last Name    | Student1 |
     | Email address | student1@example.com |
     | State / Province | Ontario    |
     | Select a country        | Canada     |
     | Male or Female          | Male       |
    And I press "Create user"
    And I follow "Home"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Enrolled users"
    And I press "Enrol users"
    And I am on homepage
    And I add the "RefinedTools" block
    Then I should see "RefinedTools"
    And I follow "Launch Adobe Connect"
    And I follow "Administration"
    And I follow "Users and Groups"
    And I press "New User"
    And I set the following fields to these values:
     | First Name | Test |
     | Last Name  | User |
     | Country    | Canada |
     | Login      | test_user |
     | New Password | automationuser |
     | Retype password | automationuser |
    And I press "Next"
    And I press "Finish"
    And I log out


  @javascript
  Scenario: Check if the user is added on LMS
    Given I log in as "admin"
    And I follow "Turn editing on"
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    And I follow "Browse list of users"
    And I set the following fields to these values:
      | User full name | contains | test |
    And I press "Add filter"
    Then I should see "test user"
    And I log out

  @javascript
  Scenario: Check if the user is added on AC
    Given I log in as "admin"
    And I am on homepage
    And I follow "Turn editing on"
    And I add the "RefinedTools" block
    Then I should see "RefinedTools"
    And I follow "Launch Adobe Connect"
    And I follow "Administration"
    And I follow "Users and Groups"
    And I press "Search"
    And I set the following fields to these values:
     | testuser |
    And I press "Information"
    And I log out





