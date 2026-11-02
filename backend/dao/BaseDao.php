<?php

require_once __DIR__ . '/Database.php';

/**
 * Reusable CRUD methods for every entity.
 * Child classes only pass the table name, primary key and writable columns.
 * All values go through PDO prepared statements; column names are checked
 * against the whitelist given by the child class.
 */
abstract class BaseDao
{
    protected PDO $connection;
    protected string $table;
    protected string $primaryKey;
    /** @var string[] Columns that insert() and update() may write */
    protected array $columns;

    public function __construct(string $table, string $primaryKey, array $columns)
    {
        $this->connection = Database::getConnection();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->columns = $columns;
    }

    /** Read: all rows. */
    public function getAll(): array
    {
        return $this->query("SELECT * FROM `{$this->table}`");
    }

    /** Read: one row by primary key, or null if it does not exist. */
    public function getById(int $id): ?array
    {
        return $this->queryOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id",
            ['id' => $id]
        );
    }

    /** Create: returns the id of the new row. */
    public function insert(array $data): int
    {
        $data = $this->filterColumns($data);
        $names = array_keys($data);
        $columnList = '`' . implode('`, `', $names) . '`';
        $placeholders = ':' . implode(', :', $names);

        $this->execute(
            "INSERT INTO `{$this->table}` ($columnList) VALUES ($placeholders)",
            $data
        );

        return (int) $this->connection->lastInsertId();
    }

    /** Update: returns the number of matching rows (0 means the id does not exist). */
    public function update(int $id, array $data): int
    {
        $data = $this->filterColumns($data);
        $assignments = [];
        foreach (array_keys($data) as $name) {
            $assignments[] = "`$name` = :$name";
        }

        $params = $data;
        $params['pk_value'] = $id;

        return $this->execute(
            "UPDATE `{$this->table}` SET " . implode(', ', $assignments)
            . " WHERE `{$this->primaryKey}` = :pk_value",
            $params
        );
    }

    /** Delete: returns the number of deleted rows (0 means the id does not exist). */
    public function delete(int $id): int
    {
        return $this->execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id",
            ['id' => $id]
        );
    }

    /** Runs a SELECT and returns all rows. */
    protected function query(string $sql, array $params = []): array
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /** Runs a SELECT and returns the first row, or null. */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    /** Runs INSERT / UPDATE / DELETE and returns the affected row count. */
    protected function execute(string $sql, array $params = []): int
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }

    private function filterColumns(array $data): array
    {
        if (empty($data)) {
            throw new InvalidArgumentException("No data given for {$this->table}.");
        }

        $unknown = array_diff(array_keys($data), $this->columns);
        if (!empty($unknown)) {
            throw new InvalidArgumentException(
                "Unknown column(s) for {$this->table}: " . implode(', ', $unknown)
            );
        }

        return $data;
    }
}
