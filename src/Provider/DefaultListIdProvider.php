<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

final class DefaultListIdProvider implements ListIdProviderInterface
{
    private string $defaultListId;

    public function __construct(string $defaultListId)
    {
        $this->defaultListId = $defaultListId;
    }

    public function getListId(): string
    {
        return $this->defaultListId;
    }
}
