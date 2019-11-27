<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Entity;

interface MailchimpListIdAwareInterface
{
    public function getListId(): ?string;
}
