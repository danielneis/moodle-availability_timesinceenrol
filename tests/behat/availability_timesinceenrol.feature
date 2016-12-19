@availability @availability_timesinceenrol
Feature: availability_timesinceenrol
  In order to control student access to activities
  As a teacher
  I need to set timesinceenrol conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
      | student2 |
    And the following "course enrolments" exist:
      | user     | course | role           | enrolstartdate
      | teacher1 | C1     | editingteacher | ##today##
      | student1 | C1     | student        | ##today##
      | student2 | C1     | student        | ##yesterday##
    And the following config values are set as admin:
      | enableavailability  | 1 |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add a Page with a timesinceenrol condition that does match.
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | Page 1 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Time since enrol" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "mintimesinceenrol" to "0"
    And I press "Save and return to course"

    # Add a Page with a timesinceenrol condition that doesn't match.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | Page 2 |
      | Description  | Test   |
      | Page content | Test   |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Time since enrol" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "mintimesinceenrol" to "1"
    And I press "Save and return to course"

    # Log back in as student1.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # Page 1 should appear, but page 2 does not.
    Then I should see "Page 1" in the "#section-1" "css_element"
    And I should not see "Page 2" in the "#section-2" "css_element"

    # Log back in as student2.
    When I log out
    And I log in as "student2"
    And I am on site homepage
    And I follow "Course 1"

    # Page 1 and Page 2 should appear.
    Then I should see "Page 1" in the "#section-1" "css_element"
    And I should see "Page 2" in the "#section-2" "css_element"
