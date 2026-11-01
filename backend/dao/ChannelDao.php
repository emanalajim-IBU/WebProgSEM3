<?php

require_once __DIR__ . '/BaseDao.php';

class ChannelDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('channels', 'channel_id', ['name', 'type']);
    }

    public function getByName(string $name): ?array
    {
        return $this->queryOne('SELECT * FROM channels WHERE name = :name', ['name' => $name]);
    }

    public function getByType(string $type): array
    {
        return $this->query('SELECT * FROM channels WHERE type = :type', ['type' => $type]);
    }
}
