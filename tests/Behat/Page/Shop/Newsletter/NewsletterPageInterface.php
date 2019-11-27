<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Page\Shop\Newsletter;

use Sylius\Behat\Page\Shop\HomePageInterface;
use Tests\Odiseo\SyliusMailchimpPlugin\Behat\Behaviour\ContainsErrorInterface;

interface NewsletterPageInterface extends HomePageInterface, ContainsErrorInterface
{
    /**
     * @param string $email
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillEmail(string $email): void;

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function subscribe(): void;
}
