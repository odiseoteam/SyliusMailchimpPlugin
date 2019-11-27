<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

final class DefaultListIdProvider implements ListIdProviderInterface
{
    /** @var string */
    private $defaultListId;

    public function __construct(string $defaultListId)
    {
        $this->defaultListId = $defaultListId;
    }

    /**
     * {@inheritdoc}
     */
    public function getListId(): string
    {
        return $this->defaultListId;
    }
}
