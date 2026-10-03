<?php

class User extends BaseModel
{
    protected string $tabel = 'users';
    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO users
                (name, email, password, role)
             VALUES
                (:name, :email, :password, :role)
             RETURNING *',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'] ?? 'admin'
            ]
        );
    }

    public function login(string $email, string $password): Respon
    {
        $result = $this->db->send_query(
            'SELECT *
             FROM users
             WHERE email = :email
             LIMIT 1',
            [
                'email' => $email
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

        /*
        |--------------------------------------------------------------------------
        | Cek password
        |--------------------------------------------------------------------------
        */

        if ($password !== $user['password']) {
            return new Respon(
                false,
                'Email atau password salah.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Hanya admin yang boleh login
        |--------------------------------------------------------------------------
        */

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
