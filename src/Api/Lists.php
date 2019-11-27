<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;

final class Lists extends MailChimp implements ListsInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        $api_key,
        LoggerInterface $logger
    ) {
        parent::__construct($api_key);

        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function addMember(string $listId, array $data): array
    {
        $response = $this->post('lists/' . $listId . '/members', $data);

        $this->logger->info('add_member: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getMember(string $listId, string $hash): array
    {
        $response = $this->get('lists/' . $listId . '/members/' . $hash);

        $this->logger->info('get_member: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateMember(string $listId, string $hash, array $data): array
    {
        $response = $this->patch('lists/' . $listId . '/members/' . $hash, $data);

        $this->logger->info('update_member: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMember(string $listId, string $hash): array
    {
        $response = $this->delete('lists/' . $listId . '/members/' . $hash);

        $this->logger->info('remove_member: '.json_encode($response));

        return $response;
    }
}
