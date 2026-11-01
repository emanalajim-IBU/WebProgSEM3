<?php

require_once __DIR__ . '/BaseDao.php';

class MessageDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('messages', 'message_id', ['channel_id', 'user_id', 'message_text']);
    }

    public function getByChannelId(int $channelId): array
    {
        return $this->query(
            'SELECT * FROM messages WHERE channel_id = :channel_id ORDER BY message_id ASC',
            ['channel_id' => $channelId]
        );
    }

    public function getByUserId(int $userId): array
    {
        return $this->query(
            'SELECT * FROM messages WHERE user_id = :user_id ORDER BY message_id ASC',
            ['user_id' => $userId]
        );
    }

    /** Messages of one channel together with the author's name (for the chat view). */
    public function getByChannelIdWithAuthor(int $channelId): array
    {
        return $this->query(
            'SELECT m.message_id, m.channel_id, m.user_id, m.message_text, u.full_name
             FROM messages m
             JOIN users u ON u.user_id = m.user_id
             WHERE m.channel_id = :channel_id
             ORDER BY m.message_id ASC',
            ['channel_id' => $channelId]
        );
    }
}
