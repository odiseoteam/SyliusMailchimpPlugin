<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;

class Lists extends MailChimp implements ListsInterface
{
    /**
     * @inheritdoc
     */
    public function addMember(string $listId, array $data): array
    {
        return $this->post('lists/'.$listId.'/members', $data);
    }

    /**
     * @inheritdoc
     */
    public function getMember(string $listId, string $hash): array
    {
        return $this->get('lists/'.$listId.'/members/'.$hash);
    }

    /**
     * @inheritdoc
     */
    public function removeMember(string $listId, string $hash): array
    {
        return $this->delete('lists/'.$listId.'/members/'.$hash);
    }
}
