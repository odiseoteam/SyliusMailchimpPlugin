<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Model;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
interface MailchimpListIdAwareInterface
{
    public function getListId(): ?string;
}
