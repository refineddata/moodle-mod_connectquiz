@refined_training @connect @sign_in_reconciliation
  Feature: sign_in_reconciliation
    In order to set sign in reconciliation
    As an admin
    I need to enable the settings for sign in reconciliation

  Background:
    Given I log in as "admin"
    And I am on homepage
    When I follow "Turn editing on"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Activity modules" node
    Then I follow "Sign-in Reconciliation"
    And I follow "Add Location"
    And I set the following fields to these values:
     | Name | Richmond Hill |
     | Time Zone | Eastern Time Zone |
    And I press "Save changes"
    And I follow "Home"
    Then I press "Add a new course"
    And I set the following fields to these values:
     | Course full name | Course 1 |
     | Course short name | C1      |
     | Course category   | Miscellaneous |
    And I press "Save changes"
    And I press "Return to course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Sign-in Reconciliation" to section "1"
    And I set the following fields to these values:
     | Name | Fun, Play and Ease in Testing |
     | Location | Toronto, Ontario          |
     | Instructor  | Jane Jones             |
     | Credits     | 3                      |
     | Icon Size   | Medium                 |
     | Icon Position | Center               |
    And I press "Save and return to course"
    And I log out
    


  @javascript
  Scenario: In order to enable Sign-in reconciliation
    Given I log in as "admin"
    And I am on homepage
    And I follow "Turn editing on"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I follow "Sign-in Reconciliation"
    Then I should see "Richmond Hill"
    Then I log out

