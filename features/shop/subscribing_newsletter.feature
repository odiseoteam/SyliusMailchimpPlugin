@newsletter
Feature: Creating new newsletter customer
    In order to be up-to-date with products and promotions
    I want to be able to subscribe to the newsletter

    Background:
        Given the store operates on a single channel in "United States"
        And there is a created list in MailChimp with specified ID

    @ui
    Scenario: Subscribing to newsletter
        Given I want to subscribe to the newsletter
        When I fill newsletter with "test@odiseo.com.ar" email
        And I subscribe to it
        Then I should be notified that I am subscribed to the newsletter
        And the email "test@odiseo.com.ar" should be exported to MailChimp's default list
