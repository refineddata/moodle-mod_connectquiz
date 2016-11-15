@refined_training @connect @connect_adding_activities
  Feature: connect_adding_activities
    In order to check how many activities can be added in a course
    As an admin
    I need to add activities in a course

  Background:
    Given I log in as "admin"
    Then I follow "General"
    And I follow "Automation Course"
    And I follow "Turn editing on"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-meetings"
    And I follow "franklu"
    And I set the following fields to these values:
     | Standard Icons | Medium |
     | Icon Position  | Left   |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-meetings"
    And I follow "secrets"
    And I set the following fields to these values:
     | Standard Icons | Large |
     | Icon Position  | Center |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-meetings"
    And I follow "othertele"
    And I set the following fields to these values:
     | Standard Icons | Small |
     | Icon Position  | Right |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Generate"
    And I set the following fields to these values:
     | Activity Name | Happy Holidays |
     | Duration      | 15:00          |
     | Standard Icons | Large         |
     | Icon Position  | Center        |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Slideshow" to section "1"
    And I follow "Shared-content"
    And I follow "CarolineTest"
    And I follow "p8mk6r5u48j"
    And I set the following fields to these values:
     | Standard Icons | Block |
     | Icon Position  | Center |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Slideshow" to section "1"
    And I follow "Shared-content"
    And I follow "Ice Breakers"
    Then I follow "p38145804"
    And I set the following fields to these values:
     | Standard Icons | Large |
     | Icon Position  | Left  |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Connect Quiz" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-content"
    And I follow "Ice Breakers"
    And I follow "p92287256"
    And I set the following fields to these values:
     | Standard Icons | Small |
     | Icon Position  | Right |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Connect Quiz" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-content"
    And I follow "Ice Breakers"
    And I follow "p23758046"
    And I set the following fields to these values:
     | Standard Icon | Small |
     | Icon Position | Center |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-Meetings"
    And I follow "videoissues"
    And I set the following fields to these values:
     | Standard Icon | Large |
     | Icon Position | Center |
    Then I press "Save and return to course"
    And I follow "Add an activity or resource"
    And I add a "Meeting" to section "1"
    And I press "Browse Adobe Connect"
    And I follow "Shared-Meetings"
    And I follow "rpwfd101"
    And I set the following fields to these values:
     | Standard Icon | Small |
     | Icon Position | Center |
    Then I press "Save and return to course"

  @javascript
  Scenario: Check if all the connect activities are added in the course
    Given I log in as "admin"
    And I am on homepage
    Then I follow "Automation Course"
    Then I should see "RPW-FD101"
    Then I log out







