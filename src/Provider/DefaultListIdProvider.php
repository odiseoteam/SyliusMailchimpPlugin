<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
final class DefaultListIdProvider implements ListIdProviderInterface
{
    /**
     * @var string
     */
    private $defaultListId;

    /**
     * @param string $defaultListId
     */
    public function __construct(string $defaultListId)
    {
        $this->defaultListId = $defaultListId;
    }

    /**
     * @return string
     */
    public function getListId(): string
    {
        return $this->defaultListId;
    }
}
