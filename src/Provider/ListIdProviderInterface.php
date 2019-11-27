<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

interface ListIdProviderInterface
{
    /**
     * @return string
     */
    public function getListId(): string;
}
