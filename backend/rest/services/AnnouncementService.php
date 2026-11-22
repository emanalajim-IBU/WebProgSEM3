<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AnnouncementDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class AnnouncementService extends BaseService
{
    private AnnouncementDao $announcementDao;
    private UserDao $userDao;

    public function __construct()
    {
        $this->announcementDao = new AnnouncementDao();
        $this->userDao = new UserDao();
        parent::__construct($this->announcementDao);
    }

    protected function entityName(): string
    {
        return 'Announcement';
    }

    /** All announcements with the author's name, newest first. */
    public function listFiltered(array $query): array
    {
        return $this->announcementDao->getAllWithAuthor();
    }

    protected function validate(array $data, bool $isCreate, ?int $id = null): array
    {
        $errors = [];
        $clean = [];

        // The author cannot change, and only admins may write announcements
        if ($isCreate) {
            $createdBy = $this->positiveInt($data, 'created_by', $errors, true);
            if ($createdBy !== null) {
                $author = $this->userDao->getById($createdBy);
                if ($author === null) {
                    $errors['created_by'] = 'user does not exist';
                } elseif ($author['role'] !== 'admin') {
                    throw new ApiException('Only admins can create announcements', 403);
                } else {
                    $clean['created_by'] = $createdBy;
                }
            }
        }

        $title = $this->readString($data, 'title', 200, $errors, $isCreate);
        if ($title !== null) {
            $clean['title'] = $title;
        }

        $content = $this->readString($data, 'content', 5000, $errors, $isCreate);
        if ($content !== null) {
            $clean['content'] = $content;
        }

        $this->throwIfErrors($errors);
        return $clean;
    }
}
