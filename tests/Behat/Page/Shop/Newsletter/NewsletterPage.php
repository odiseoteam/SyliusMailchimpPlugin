<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Page\Shop\Newsletter;

use Sylius\Behat\Page\Shop\HomePage;
use Tests\Odiseo\SyliusMailchimpPlugin\Behat\Behaviour\ContainsErrorTrait;

final class NewsletterPage extends HomePage implements NewsletterPageInterface
{
    use ContainsErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function fillEmail(string $email): void
    {
        $this->getDocument()->fillField('newsletter[email]', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(): void
    {
        $this->getDocument()->pressButton('Subscribe');
    }
}
