<?php

class Ticket
{
    private ?int $id;
    private int $eventId;
    private string $name;
    private float $price;
    private int $stock;

    public function __construct(?int $id, int $eventId, string $name, float $price, int $stock)
    {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getId(): ?int { return $this->id; }
    public function getEventId(): int { return $this->eventId; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
    public function getStock(): int { return $this->stock; }

    public function setName(string $name): void { $this->name = $name; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function setStock(int $stock): void { $this->stock = $stock; }
}