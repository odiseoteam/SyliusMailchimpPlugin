<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

interface ListIdProviderInterface
{
    public function getListId(): string;
}
