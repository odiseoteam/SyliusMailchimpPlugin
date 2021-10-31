<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface ListsInterface
{
    public function addMember(string $listId, array $data): array;

    public function getMember(string $listId, string $hash): array;

    public function updateMember(string $listId, string $hash, array $data): array;

    public function removeMember(string $listId, string $hash): array;
}
