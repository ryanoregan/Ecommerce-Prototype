<?php

namespace Agora\Database;

interface IDatabase
{
    // Method to execute a query and return results
    public function query(string $sql);

    // Method to execute a query (e.g., INSERT, UPDATE, DELETE) without returning results
    public function execute(string $sql): bool;

    // Method to execute a batch of SQL queries
    public function executeBatch(array $list): bool;

    // Method to get the last inserted ID after an INSERT query
    public function getInsertID(): int;

    // Method to close the database connection
    public function close(): void;

    // Transaction management: begin a transaction
    public function beginTransaction(): void;

    // Transaction management: commit the current transaction
    public function commitTransaction(): void;

    // Transaction management: roll back the current transaction
    public function rollbackTransaction(): void;

    // Method to execute a prepared query with bound parameters and return results
    public function queryPrepared(string $parameterisedSQL, array $fields);

    // Method to execute a prepared query (INSERT, UPDATE, DELETE) with bound parameters
    public function executePrepared(string $parameterisedSQL, array $fields): bool;
}