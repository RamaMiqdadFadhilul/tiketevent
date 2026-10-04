<?php

class User extends BaseModel
{
    protected string $tabel = 'users';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO users
                (
                    name,
                    email,
                    password,
                    role
                )
             VALUES
                (
                    $1,
                    $2,
                    $3,
                    $4
                )
             RETURNING *',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'] ?? 'admin'
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE users
             SET
                name = $1,
                email = $2,
                password = $3,
                role = $4
             WHERE id = $5',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'] ?? 'admin',
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        return $this->db->send_query(
            'DELETE FROM users
             WHERE id = $1',
            [
                $id
            ]
        );
    }

    public function login(
        string $email,
        string $password
    ): Respon {

        $result = $this->db->send_query(
            'SELECT *
             FROM users
             WHERE email = $1
             LIMIT 1',
            [
                $email
            ]
        );

        if (!$result->success) {
            return new Respon(
                false,
                $result->message
            );
        }

        if (empty($result->data)) {
            return new Respon(
                false,
                'Email atau password salah.'
            );
        }

        $user = $result->data[0];

        if ($password !== $user['password']) {
            return new Respon(
                false,
                'Email atau password salah.'
            );
        }

        if ($user['role'] !== 'admin') {
            return new Respon(
                false,
                'Akun tidak memiliki akses admin.'
            );
        }

        return new Respon(
            true,
            'Login berhasil.',
            [$user]
        );
    }
}
