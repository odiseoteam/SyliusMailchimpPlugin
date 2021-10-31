<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;

final class Lists extends MailChimp implements ListsInterface
{
    private LoggerInterface $logger;

    public function __construct(
        string $api_key,
        LoggerInterface $logger
    ) {
        parent::__construct($api_key);

        $this->logger = $logger;
    }

    public function addMember(string $listId, array $data): array
    {
        $response = $this->post('lists/' . $listId . '/members', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_member: ' . json_encode($response));

        return $response;
    }

    public function getMember(string $listId, string $hash): array
    {
        $response = $this->get('lists/' . $listId . '/members/' . $hash);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_member: ' . json_encode($response));

        return $response;
    }

    public function updateMember(string $listId, string $hash, array $data): array
    {
        $response = $this->patch('lists/' . $listId . '/members/' . $hash, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_member: ' . json_encode($response));

        return $response;
    }

    public function removeMember(string $listId, string $hash): array
    {
        $response = $this->delete('lists/' . $listId . '/members/' . $hash);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_member: ' . json_encode($response));

        return $response;
    }
}
