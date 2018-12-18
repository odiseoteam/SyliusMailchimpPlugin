<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface EcommerceInterface
{
    public function addStore(array $data): array;

    public function getStores(): array;

    public function getStore(string $storeId): array;

    public function updateStore(string $storeId, array $data): array;

    public function removeStore(string $storeId): array;

    public function addCustomer(string $storeId, array $data): array;

    public function getCustomers(string $storeId): array ;

    public function getCustomer(string $storeId, string $customerId): array;

    public function updateCustomer(string $storeId, string $customerId, array $data): array;

    public function removeCustomer(string $storeId, string $customerId): array;

    public function addProduct(string $storeId, array $data): array;

    public function getProduct(string $storeId, string $productId): array;

    public function updateProduct(string $storeId, string $productId, array $data): array;

    public function removeProduct(string $storeId, string $productId): array;

    public function addOrder(string $storeId, array $data): array;

    public function getOrders(): array;

    public function getOrdersByStore(string $storeId): array;

    public function getOrder(string $storeId, string $orderId): array;

    public function updateOrder(string $storeId, string $orderId, array $data): array;

    public function removeOrder(string $storeId, string $orderId): array;

    public function addCart(string $storeId, array $data): array;

    public function getCarts(string $storeId): array;

    public function getCart(string $storeId, string $cartId): array;

    public function updateCart(string $storeId, string $cartId, array $data): array;

    public function removeCart(string $storeId, string $cartId): array;
}
