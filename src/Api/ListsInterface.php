<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface ListsInterface
{
    /**
     * @param string $listId
     * @param array $data
     */
    public function addMember(string $listId, array $data);

    /**
     * @param string $listId
     * @param string $hash
     */
    public function getMember(string $listId, string $hash);

    /**
     * @param string $listId
     * @param string $hash
     * @param array $data
     */
    public function updateMember(string $listId, string $hash, array $data);

    /**
     * @param string $listId
     * @param string $hash
     */
    public function removeMember(string $listId, string $hash);
}
