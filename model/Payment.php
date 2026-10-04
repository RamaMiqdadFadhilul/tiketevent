<?php

class Payment extends BaseModel
{
    protected string $tabel = 'payments';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO payments
                (
                    order_id,
                    payment_method,
                    amount,
                    status,
                    paid_at
                )
             VALUES
                (
                    $1,
                    $2,
                    $3,
                    $4,
                    $5
                )
             RETURNING *',
            [
                $data['order_id'],
                $data['payment_method'],
                $data['amount'],
                $data['status'] ?? 'pending',
                $data['paid_at'] ?? null
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE payments
             SET
                order_id = $1,
                payment_method = $2,
                amount = $3,
                status = $4,
                paid_at = $5
             WHERE id = $6',
            [
                $data['order_id'],
                $data['payment_method'],
                $data['amount'],
                $data['status'] ?? 'pending',
                $data['paid_at'] ?? null,
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        return $this->db->send_query(
            'DELETE FROM payments
             WHERE id = $1',
            [
                $id
            ]
        );
    }
}
