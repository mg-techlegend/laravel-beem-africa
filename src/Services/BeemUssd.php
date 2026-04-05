<?php

namespace TechLegend\LaravelBeemAfrica\Services;

use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\BeemAfricaClient;
use TechLegend\LaravelBeemAfrica\Data\UssdResponse;

final class BeemUssd
{
    public function __construct(
        private readonly BeemAfricaClient $client,
    ) {}

    public function createMenu(array $menuData): UssdResponse
    {
        if (! isset($menuData['menu_name']) || trim($menuData['menu_name']) === '') {
            throw new InvalidArgumentException('[Beem Africa] Menu name is required.');
        }

        $payload = [
            'menu_name' => $menuData['menu_name'],
            'menu_items' => $menuData['menu_items'] ?? [],
            'welcome_message' => $menuData['welcome_message'] ?? 'Welcome to our USSD service',
            'goodbye_message' => $menuData['goodbye_message'] ?? 'Thank you for using our service',
        ];

        $response = $this->client->post('/ussd/menus', $payload);

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }

    public function updateMenu(string $menuId, array $menuData): UssdResponse
    {
        if (trim($menuId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Menu ID cannot be empty.');
        }

        $payload = array_filter([
            'menu_name' => $menuData['menu_name'] ?? null,
            'menu_items' => $menuData['menu_items'] ?? null,
            'welcome_message' => $menuData['welcome_message'] ?? null,
            'goodbye_message' => $menuData['goodbye_message'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->put('/ussd/menus/' . rawurlencode($menuId), $payload);

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }

    public function getMenu(string $menuId): UssdResponse
    {
        if (trim($menuId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Menu ID cannot be empty.');
        }

        $response = $this->client->get('/ussd/menus/' . rawurlencode($menuId));

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }

    public function listMenus(array $filters = []): UssdResponse
    {
        $query = array_filter([
            'page' => $filters['page'] ?? null,
            'limit' => $filters['limit'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->client->get('/ussd/menus', $query);

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }

    public function deleteMenu(string $menuId): UssdResponse
    {
        if (trim($menuId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Menu ID cannot be empty.');
        }

        $response = $this->client->delete('/ussd/menus/' . rawurlencode($menuId));

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }

    public function getSessionData(string $sessionId): UssdResponse
    {
        if (trim($sessionId) === '') {
            throw new InvalidArgumentException('[Beem Africa] Session ID cannot be empty.');
        }

        $response = $this->client->get('/ussd/sessions/' . rawurlencode($sessionId));

        return $this->client->handleJsonResponse($response, [UssdResponse::class, 'fromApiPayload']);
    }
}
