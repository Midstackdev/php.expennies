<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('receipts')]
class Receipt
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name: 'file_name')]
    private string $fileName;

    #[Column(name: 'created_at')]
    private \DateTime $createdAt;

    #[ManyToOne(inversedBy: 'receipts')]
    private Transaction $transaction;

    public function getId()
    {
        return $this->id;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction)
    {
        $transaction->addReceipt($this);
        $this->transaction = $transaction;
        return $this;
    }
}
