<?php

interface Crudable
{
    public function find_all(): array;

    public function find_by_id(int $id): ?array;

    public function insert(array $data): Respon;
}
