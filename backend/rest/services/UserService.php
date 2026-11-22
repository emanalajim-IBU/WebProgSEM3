<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService
{
    private UserDao $userDao;

    public function __construct()
    {
        $this->userDao = new UserDao();
        parent::__construct($this->userDao);
    }

    protected function entityName(): string
    {
        return 'User';
    }

    protected function validate(array $data, bool $isCreate, ?int $id = null): array
    {
        $errors = [];
        $clean = [];

        $fullName = $this->readString($data, 'full_name', 100, $errors, $isCreate);
        if ($fullName !== null) {
            $clean['full_name'] = $fullName;
        }

        $email = $this->readString($data, 'email', 150, $errors, $isCreate);
        if ($email !== null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'email must be a valid email address';
            } else {
                $existing = $this->userDao->getByEmail($email);
                if ($existing !== null && (int) $existing['user_id'] !== $id) {
                    $errors['email'] = 'A user with this email already exists';
                } else {
                    $clean['email'] = $email;
                }
            }
        }

        // The password is hashed here and only the hash is stored
        if (array_key_exists('password', $data)) {
            $password = $data['password'];
            if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
                $errors['password'] = 'password must be between 8 and 72 characters';
            } else {
                $clean['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
        } elseif ($isCreate) {
            $errors['password'] = 'password is required';
        }

        $role = $this->oneOf($data, 'role', ['admin', 'resident'], $errors, false, $isCreate ? 'resident' : null);
        if ($role !== null) {
            $clean['role'] = $role;
        }

        $this->throwIfErrors($errors);
        return $clean;
    }
}
