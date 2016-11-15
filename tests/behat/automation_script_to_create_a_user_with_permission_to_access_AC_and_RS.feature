@refined_training @connect @create_a_user_with_permission_to_access_AC_and_RS
  Feature: create_a_user_with_permission_to_access_AC_and_RS
    In order to create a user with permission to access AC and RS
    As an admin
    I need to navigate to Refined Settings page and connect AC and RS

  Background:
    Given the following "connectserviceaccount" exist:
      | service_username |
      | acceptance_test_site_connect | 
    Given I log in as "admin"
    And I am on homepage
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Local plugins" node
    And I follow "Refined Services"
    And I wait "5" seconds
    And I press "Update Users on Refined Services"
    And I wait "5" seconds
    And I expand "Activity modules" node
    And I follow "Connect Activity"
    And I set the following fields to these values:
     | Update on Connect Central | 1 |
    And I press "Save changes"
    And I expand "Users" node
    And I expand "Accounts" node
    And I follow "Add a new user"
    And I set the following fields to these values:
     | Username | automationtest |
     | New password | Abc@1234   |
     | First name   | Automation |
     | Last name    | Test       |
     | Email address | automation.test@example.com |
     | State / Province | Ontario          |
     | Select a country         | Canada           |
     | Male or Female           | Male             |
    And I press "Create user"
    And I am on homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "RefinedTools" block
    Then I should see "RefinedTools"
    #And I follow "Launch Adobe Connect"
    #And I follow "Administration"
    #And I follow "Users and Groups"
    #And I follow "Search"
    #And I set the following fields to these values:
    # | Search | Automation |
    #And I follow "AutomationTest"
    #And I press "Automation Test"
    #And I press "Information"
    #And I press "Users and Groups"
    #And I press "New User"
    #And I set the following fields to these values:
    # | First Name | Automationqa |
    # | Last Name  | Test         |
    # | Company    | Refined Data |
    # | City       | Richmond Hill |
    # | State      | Ontario       |
    # | Login      | automationqa@example.com  |
    # | New Password | Abc@1234    |
    # | Retype Password | Abc@1234 |
    #And I press "Finish"
    And I log out

  @javascript
  Scenario: Check if the user with permission to access AC and RS
    Given I am on homepage
    When I follow "Log in"
    And I set the field "Username" to "automationtest"
    And I set the field "Password" to "Abc@1234"
    And I press "Log in"
    And I expand "My profile settings" node
    And I follow "Edit profile"
    And I press "Update profile"
    Then I should not see "RS001"
    And I log out