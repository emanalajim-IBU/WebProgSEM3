<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ChannelDao.php';

class ChannelService extends BaseService
{
    private ChannelDao $channelDao;

    public function __construct()
    {
        $this->channelDao = new ChannelDao();
        parent::__construct($this->channelDao);
    }

    protected function entityName(): string
    {
        return 'Channel';
    }

    protected function validate(array $data, bool $isCreate, ?int $id = null): array
    {
        $errors = [];
        $clean = [];

        $name = $this->readString($data, 'name', 100, $errors, $isCreate);
        if ($name !== null) {
            $existing = $this->channelDao->getByName($name);
            if ($existing !== null && (int) $existing['channel_id'] !== $id) {
                $errors['name'] = 'A channel with this name already exists';
            } else {
                $clean['name'] = $name;
            }
        }

        $type = $this->oneOf($data, 'type', ['public', 'private'], $errors, false, $isCreate ? 'public' : null);
        if ($type !== null) {
            $clean['type'] = $type;
        }

        $this->throwIfErrors($errors);
        return $clean;
    }
}
