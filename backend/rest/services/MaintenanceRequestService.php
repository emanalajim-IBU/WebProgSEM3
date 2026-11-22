<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MaintenanceRequestDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class MaintenanceRequestService extends BaseService
{
    private const STATUSES = ['open', 'in_progress', 'resolved'];

    private MaintenanceRequestDao $requestDao;
    private UserDao $userDao;

    public function __construct()
    {
        $this->requestDao = new MaintenanceRequestDao();
        $this->userDao = new UserDao();
        parent::__construct($this->requestDao);
    }

    protected function entityName(): string
    {
        return 'Maintenance request';
    }

    /** All requests with the requester's name; optional filters: status, user_id. */
    public function listFiltered(array $query): array
    {
        $rows = $this->requestDao->getAllWithRequester();

        if (isset($query['status'])) {
            if (!in_array($query['status'], self::STATUSES, true)) {
                throw new ApiException('status must be one of: ' . implode(', ', self::STATUSES), 400);
            }
            $status = $query['status'];
            $rows = array_values(array_filter($rows, fn ($row) => $row['status'] === $status));
        }

        if (isset($query['user_id'])) {
            $userId = $this->queryId($query, 'user_id');
            $rows = array_values(array_filter($rows, fn ($row) => (int) $row['user_id'] === $userId));
        }

        return $rows;
    }

    protected function validate(array $data, bool $isCreate, ?int $id = null): array
    {
        $errors = [];
        $clean = [];

        // The requester cannot change after the request is created
        if ($isCreate) {
            $userId = $this->positiveInt($data, 'user_id', $errors, true);
            if ($userId !== null) {
                if ($this->userDao->getById($userId) === null) {
                    $errors['user_id'] = 'user does not exist';
                } else {
                    $clean['user_id'] = $userId;
                }
            }
        }

        $title = $this->readString($data, 'title', 200, $errors, $isCreate);
        if ($title !== null) {
            $clean['title'] = $title;
        }

        $status = $this->oneOf($data, 'status', self::STATUSES, $errors, false, $isCreate ? 'open' : null);
        if ($status !== null) {
            $clean['status'] = $status;
        }

        $this->throwIfErrors($errors);
        return $clean;
    }
}
