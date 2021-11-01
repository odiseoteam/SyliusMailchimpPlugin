<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Fake;

use DrewM\MailChimp\MailChimp;

final class MailchimpApi extends MailChimp
{
    public function post($method, $args = array(), $timeout = self::TIMEOUT)
    {
        return ['status' => 'subscribed'];
    }

    public function get($method, $args = array(), $timeout = self::TIMEOUT)
    {
        return ['status' => 'subscribed'];
    }
}
