<?php

require_once __DIR__ . '/BaseDao.php';

class AnnouncementDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('announcements', 'announcement_id', ['created_by', 'title', 'content']);
    }

    public function getByCreatedBy(int $userId): array
    {
        return $this->query(
            'SELECT * FROM announcements WHERE created_by = :created_by ORDER BY announcement_id DESC',
            ['created_by' => $userId]
        );
    }

    /** All announcements together with the author's name, newest first. */
    public function getAllWithAuthor(): array
    {
        return $this->query(
            'SELECT a.announcement_id, a.created_by, a.title, a.content, u.full_name
             FROM announcements a
             JOIN users u ON u.user_id = a.created_by
             ORDER BY a.announcement_id DESC'
        );
    }
}
