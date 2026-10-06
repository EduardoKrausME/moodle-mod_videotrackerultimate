@mod @mod_videotrackerultimate
Feature: Configure explainable Video Tracker Ultimate indicators
  In order to keep Engagement Score auditable
  As a teacher
  I need to build score rules from a fixed rule catalogue

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher1@example.com |
      | student1 | Student | One | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Tracker course | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name | course |
      | videotrackerultimate | Evidence video | C1 |

  Scenario: Teacher creates a weighted percentage indicator
    Given I am on the "Evidence video" "videotrackerultimate activity" page logged in as teacher1
    When I follow "Indicators"
    And I follow "Add indicator"
    And I set the following fields to these values:
      | Name | Coverage |
      | Rule type | Watched percentage is at least X |
      | Weight | 35 |
      | Limit | 90 |
      | Enabled | 1 |
    And I press "Save indicator"
    Then I should see "Coverage"
    And I should see "35"
    And I should see "90"

  Scenario: Public learner view does not expose a ranking
    Given I am on the "Evidence video" "videotrackerultimate activity" page logged in as student1
    Then I should not see "Teacher-only ranking"
