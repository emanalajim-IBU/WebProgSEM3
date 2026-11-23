<?php

require_once __DIR__ . '/BaseDao.php';

class MaintenanceRequestDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('maintenance_requests', 'request_id', ['user_id', 'title', 'status']);
    }

    public function getByUserId(int $userId): array
    {
        return $this->query(
            'SELECT * FROM maintenance_requests WHERE user_id = :user_id ORDER BY request_id ASC',
            ['user_id' => $userId]
        );
    }

    public function getByStatus(string $status): array
    {
        return $this->query(
            'SELECT * FROM maintenance_requests WHERE status = :status ORDER BY request_id ASC',
            ['status' => $status]
        );
    }

    /** All requests together with the requester's name (for the maintenance table). */
    public function getAllWithRequester(): array
    {
        return $this->query(
            'SELECT r.request_id, r.user_id, r.title, r.status, u.full_name
             FROM maintenance_requests r
             JOIN users u ON u.user_id = r.user_id
             ORDER BY r.request_id ASC'
        );
    }
}
