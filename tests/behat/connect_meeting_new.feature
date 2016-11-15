@refined_training @connect @connect_meeting_new
  Feature: connect_meeting_new
    In order to view meeting
    As an admin
    I need to add a meeting

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
     | Course short name | QA               |
     | Course category   | Miscellaneous    |
     | Visible           | Show             |
    And I press "Save changes"
    And I press "Return to course"    
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    Then I press "Generate"
    And I set the following fields to these values:
     | Activity Name | Test Meeting Automation |
    And I wait "5" seconds    
    And I should see "The requested name ( Test Meeting Automation ) is found and can not be used"
    And I press "Cancel"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    Then I press "Browse Adobe Connect"
    Then I follow "Shared-Meetings"
    Then I follow "testmeetingautomation"
    And I follow "Expand all"
    And I set the following fields to these values:
     | Duration | 00:30 |
     | Display on course page | 1 |
     | Standard Icons         | Medium |
     | Icon Position          | Right  |
     | Grading Options        | Adobe Grading |
    And I press "Save and return to course"
    And I wait "5" seconds
    Then I log out



  @javascript
  Scenario: Check if the meeting is added in the course
    Given I log in as "admin"
    And I am on homepage
    And I follow "Automation Course"
    Then I should see "Test Meeting Automation"
    Then I log out
