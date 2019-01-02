<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Provider;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
interface ListIdProviderInterface
{
    /**
     * @return string
     */
    public function getListId(): string;
}
