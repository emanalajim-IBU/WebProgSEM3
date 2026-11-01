<?php

require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    //making this variable means that the passwords are safe.
    private const SAFE_COLUMNS = 'user_id, full_name, email, role';

    public function __construct()
    {
        parent::__construct('users', 'user_id', ['full_name', 'email', 'password_hash', 'role']);
    }

    public function getAll(): array
    {
        return $this->query('SELECT ' . self::SAFE_COLUMNS . ' FROM users');
    }

    public function getById(int $id): ?array
    {
        return $this->queryOne(
            'SELECT ' . self::SAFE_COLUMNS . ' FROM users WHERE user_id = :id',
            ['id' => $id]
        );
    }

    public function getByEmail(string $email): ?array
    {
        return $this->queryOne('SELECT ' . self::SAFE_COLUMNS . ' FROM users WHERE email = :email', ['email' => $email]);
    }

    public function getByRole(string $role): array
    {
        return $this->query('SELECT ' . self::SAFE_COLUMNS . ' FROM users WHERE role = :role', ['role' => $role]);
    }

    //only way to retrieve the passwords when they are needed.
    public function getByEmailForLogin(string $email): ?array
    {
        return $this->queryOne('SELECT * FROM users WHERE email = :email', ['email' => $email]);
    }
}
