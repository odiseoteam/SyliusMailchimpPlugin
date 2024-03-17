<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

final class DefaultListIdProvider implements ListIdProviderInterface
{
    public function __construct(
        private string $defaultListId
    ) {
    }

    public function getListId(): string
    {
        return $this->defaultListId;
    }
}
