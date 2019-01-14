<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Page\Shop\Newsletter;

use Sylius\Behat\Page\Shop\HomePage;
use Tests\Odiseo\SyliusMailchimpPlugin\Behat\Behaviour\ContainsErrorTrait;

class NewsletterPage extends HomePage implements NewsletterPageInterface
{
    use ContainsErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function fillEmail($email)
    {
        $this->getDocument()->fillField('newsletter[email]', $email);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        $this->getDocument()->pressButton('Subscribe');
    }
}
