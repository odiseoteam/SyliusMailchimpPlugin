<?php

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