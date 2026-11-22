<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MessageDao.php';
require_once __DIR__ . '/../dao/ChannelDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class MessageService extends BaseService
{
    private MessageDao $messageDao;
    private ChannelDao $channelDao;
    private UserDao $userDao;

    public function __construct()
    {
        $this->messageDao = new MessageDao();
        $this->channelDao = new ChannelDao();
        $this->userDao = new UserDao();
        parent::__construct($this->messageDao);
    }

    protected function entityName(): string
    {
        return 'Message';
    }

    public function listFiltered(array $query): array
    {
        if (isset($query['channel_id'])) {
            return $this->getByChannel($this->queryId($query, 'channel_id'));
        }
        if (isset($query['user_id'])) {
            return $this->messageDao->getByUserId($this->queryId($query, 'user_id'));
        }
        return $this->messageDao->getAll();
    }

    /** All messages of one channel, with the author's name. */
    public function getByChannel(int $channelId): array
    {
        if ($this->channelDao->getById($channelId) === null) {
            throw new ApiException('Channel not found', 404);
        }
        return $this->messageDao->getByChannelIdWithAuthor($channelId);
    }

    protected function validate(array $data, bool $isCreate, ?int $id = null): array
    {
        $errors = [];
        $clean = [];

        // The channel and the author cannot change after a message is created
        if ($isCreate) {
            $channelId = $this->positiveInt($data, 'channel_id', $errors, true);
            if ($channelId !== null) {
                if ($this->channelDao->getById($channelId) === null) {
                    $errors['channel_id'] = 'channel does not exist';
                } else {
                    $clean['channel_id'] = $channelId;
                }
            }

            $userId = $this->positiveInt($data, 'user_id', $errors, true);
            if ($userId !== null) {
                if ($this->userDao->getById($userId) === null) {
                    $errors['user_id'] = 'user does not exist';
                } else {
                    $clean['user_id'] = $userId;
                }
            }
        }

        $text = $this->readString($data, 'message_text', 2000, $errors, $isCreate);
        if ($text !== null) {
            $clean['message_text'] = $text;
        }

        $this->throwIfErrors($errors);
        return $clean;
    }
}
