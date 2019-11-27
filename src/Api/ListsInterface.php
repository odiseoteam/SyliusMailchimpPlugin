<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface ListsInterface
{
    /**
     * @param string $listId
     * @param array $data
     * @return array|false
     */
    public function addMember(string $listId, array $data): array;

    /**
     * @param string $listId
     * @param string $hash
     * @return array|false
     */
    public function getMember(string $listId, string $hash): array;

    /**
     * @param string $listId
     * @param string $hash
     * @param array $data
     * @return array|false
     */
    public function updateMember(string $listId, string $hash, array $data): array;

    /**
     * @param string $listId
     * @param string $hash
     * @return array|false
     */
    public function removeMember(string $listId, string $hash): array;
}
