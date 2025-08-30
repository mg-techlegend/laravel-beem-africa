<?php

namespace TechLegend\LaravelBeemAfrica\USSD;

use TechLegend\LaravelBeemAfrica\BeemApiClient;

class BeemUssd extends BeemApiClient
{
    public function createMenu(array $menuData): array
    {
        $payload = [
            'menu_name' => $menuData['menu_name'],
            'menu_items' => $menuData['menu_items'] ?? [],
            'welcome_message' => $menuData['welcome_message'] ?? 'Welcome to our USSD service',
            'goodbye_message' => $menuData['goodbye_message'] ?? 'Thank you for using our service',
        ];

        $response = $this->makeRequest('POST', $this->getEndpoint('ussd') . '/menus', $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'menu_id' => $data['menu_id'] ?? null,
                'message' => $data['message'] ?? 'USSD menu created successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'menu_id' => null,
            'message' => 'USSD menu creation failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function updateMenu(string $menuId, array $menuData): array
    {
        $payload = [
            'menu_name' => $menuData['menu_name'] ?? null,
            'menu_items' => $menuData['menu_items'] ?? null,
            'welcome_message' => $menuData['welcome_message'] ?? null,
            'goodbye_message' => $menuData['goodbye_message'] ?? null,
        ];

        // Remove null values
        $payload = array_filter($payload, function ($value) {
            return $value !== null;
        });

        $response = $this->makeRequest('PUT', $this->getEndpoint('ussd') . '/menus/' . $menuId, $payload);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'menu_id' => $data['menu_id'] ?? $menuId,
                'message' => $data['message'] ?? 'USSD menu updated successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'menu_id' => $menuId,
            'message' => 'USSD menu update failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getMenu(string $menuId): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('ussd') . '/menus/' . $menuId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'menu' => $data,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'menu' => null,
            'message' => 'Failed to get USSD menu: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function listMenus(array $filters = []): array
    {
        $queryParams = [];
        
        if (isset($filters['page'])) {
            $queryParams['page'] = $filters['page'];
        }
        
        if (isset($filters['limit'])) {
            $queryParams['limit'] = $filters['limit'];
        }

        $endpoint = $this->getEndpoint('ussd') . '/menus';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'menus' => $data['menus'] ?? [],
                'total' => $data['total'] ?? 0,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'menus' => [],
            'message' => 'Failed to list USSD menus: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function deleteMenu(string $menuId): array
    {
        $response = $this->makeRequest('DELETE', $this->getEndpoint('ussd') . '/menus/' . $menuId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'message' => $data['message'] ?? 'USSD menu deleted successfully',
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'message' => 'USSD menu deletion failed: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }

    public function getSessionData(string $sessionId): array
    {
        $response = $this->makeRequest('GET', $this->getEndpoint('ussd') . '/sessions/' . $sessionId);

        if ($response['successful']) {
            $data = $response['data'];
            return [
                'successful' => true,
                'session_data' => $data,
                'status_code' => $response['status_code'],
                'data' => $data,
            ];
        }

        return [
            'successful' => false,
            'session_data' => null,
            'message' => 'Failed to get session data: ' . ($response['error'] ?? 'Unknown error'),
            'status_code' => $response['status_code'],
            'error' => $response['error'] ?? null,
        ];
    }
}
