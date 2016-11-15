@refined_training @connect @connect_activity_meeting_user
  Feature: connect_activity_meeting_user
    In order to achieve the grading
    As an user
    I need to complete the test course as a user

  Background:        
    ##RT_START #Connect services settings 
    Given the following "connectserviceaccount" exist:
      | service_username |
      | acceptance_test_site_connect | 
    Given I log in as "admin"    
    And I am on homepage
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Activity modules" node
    And I follow "Connect Activity"
    And I set the following fields to these values:
     | Update on Connect Central | 1 |
     | Allow Icon Display | 1 |
    And I press "Save changes"
    And I am on homepage 
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Filters" node
    And I follow "Manage filters"
    And I click on "On" "option" in the "Connect" "table_row"
    And I am on homepage
    ##RT_END #Connect services settings
    And I follow "Turn editing on"
    And I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Automation Course |
     | Course short name | QA |
     | Course category   | Miscellaneous |
     | Visible           | Show |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    Then I press "Browse Adobe Connect"
    Then I follow "Shared-Meetings"
    Then I follow "testmeetingautomation"
    And I set the following fields to these values:
     | Duration      | 00:30                   |
    And I press "Save and return to course"   
    And I expand "My profile" node
    And I follow "View profile" 
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    And I follow "Add a new user"
    And I set the following fields to these values:
     | Username | testuser |
     | New password | Abc@1234 |
     | First name   | testuser      |
     | Last name      | 1    |
     | Email address | testuser@example.com |
     | State / Province | Ontario     |
     | Select a country        | Canada      |
     | Male or Female          | Male        |
     | Preferred language      | English (en) |
    And I press "Create user" 
    And I am on homepage
    And I follow "Automation Course"
    And I expand "Users" node
    And I follow "Enrolled users" 
    Then I press "Enrol users" 
    #And I wait "500" seconds
    And I set the following fields to these values:
     | Not enrolled users | testuser 1 (testuser@example.com) |
    And I press "Add"
    And I log out
    #Check if connect activity meeting user is added in the course
    Given I am on homepage
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "Abc@1234"
    And I press "Log in"
    Then I follow "Automation Course"
    Then I follow "Test Meeting Automation"
    And I wait "5" seconds 
    And I click on ".connect_tooltip img" "css_element"
    And I wait "5" seconds 
    And I log out

  @javascript
  Scenario: Log in as a admin to check the grades
    Given I log in as "admin"
    Then I follow "Automation Course"
    And I follow "Grades"
    And I should see "100.00" in the ".gradevalue" "css_element"
    And I log out



    