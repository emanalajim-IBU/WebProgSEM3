<?php

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/../dao/BaseDao.php';

/**
 * Shared service logic: reading, creating, updating and deleting with validation.
 * Child services only describe their own validation rules.
 * Services are the only layer that calls the DAO classes.
 */
abstract class BaseService
{
    protected BaseDao $dao;

    public function __construct(BaseDao $dao)
    {
        $this->dao = $dao;
    }

    /** Name used in "not found" messages, for example "User". */
    abstract protected function entityName(): string;

    /**
     * Checks the input and returns only the allowed, cleaned columns.
     * $isCreate is true when a new row is created; $id is the row being updated.
     */
    abstract protected function validate(array $data, bool $isCreate, ?int $id = null): array;

    public function getAll(): array
    {
        return $this->dao->getAll();
    }

    /** List for GET requests; child services add query filters. */
    public function listFiltered(array $query): array
    {
        return $this->getAll();
    }

    public function getById(int $id): array
    {
        $row = $this->dao->getById($id);
        if ($row === null) {
            throw new ApiException($this->entityName() . ' not found', 404);
        }
        return $row;
    }

    public function create(array $data): array
    {
        $clean = $this->validate($data, true);
        $id = $this->dao->insert($clean);
        return $this->getById($id);
    }

    public function update(int $id, array $data): array
    {
        $this->getById($id);
        $clean = $this->validate($data, false, $id);
        if (empty($clean)) {
            throw new ApiException('No valid fields to update', 400);
        }
        $this->dao->update($id, $clean);
        return $this->getById($id);
    }

    public function delete(int $id): void
    {
        $this->getById($id);
        $this->dao->delete($id);
    }

    // ----- validation helpers -----

    protected function throwIfErrors(array $errors): void
    {
        if (!empty($errors)) {
            throw new ApiException('Validation failed', 400, $errors);
        }
    }

    protected function readString(array $data, string $field, int $max, array &$errors, bool $required): ?string
    {
        if (!array_key_exists($field, $data)) {
            if ($required) {
                $errors[$field] = "$field is required";
            }
            return null;
        }
        if (!is_string($data[$field])) {
            $errors[$field] = "$field must be a string";
            return null;
        }
        $value = trim($data[$field]);
        if ($value === '' || mb_strlen($value) > $max) {
            $errors[$field] = "$field must be between 1 and $max characters";
            return null;
        }
        return $value;
    }

    protected function positiveInt(array $data, string $field, array &$errors, bool $required): ?int
    {
        if (!array_key_exists($field, $data)) {
            if ($required) {
                $errors[$field] = "$field is required";
            }
            return null;
        }
        $value = $data[$field];
        if (is_string($value) && ctype_digit($value)) {
            $value = (int) $value;
        }
        if (!is_int($value) || $value < 1) {
            $errors[$field] = "$field must be a positive integer";
            return null;
        }
        return $value;
    }

    protected function oneOf(array $data, string $field, array $allowed, array &$errors, bool $required, ?string $default = null): ?string
    {
        if (!array_key_exists($field, $data)) {
            if ($default !== null) {
                return $default;
            }
            if ($required) {
                $errors[$field] = "$field is required";
            }
            return null;
        }
        if (!is_string($data[$field]) || !in_array($data[$field], $allowed, true)) {
            $errors[$field] = "$field must be one of: " . implode(', ', $allowed);
            return null;
        }
        return $data[$field];
    }

    /** Reads a positive integer from the query string (for filters). */
    protected function queryId(array $query, string $key): int
    {
        $value = $query[$key] ?? null;
        if (!is_string($value) || !ctype_digit($value) || (int) $value < 1) {
            throw new ApiException("$key must be a positive integer", 400);
        }
        return (int) $value;
    }
}
