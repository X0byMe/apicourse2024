<?php

namespace App\Entity;

use App\Entity\Customer;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\InvoiceIncrementationController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    paginationEnabled:true, 
    paginationItemsPerPage: 10, 
    order: ['amount'=>'asc'],
    normalizationContext: [
        'groups' => ['invoices_read']
    ],
    operations : [
        new Get(),
        new Post(),
        new Post(
            controller:InvoiceIncrementationController::class,
            uriTemplate: '/invoices/{id}/increment',
            name: 'Increment',
            openapiContext:[
                'summary' => "Incrémente une facture",
                'description' => "Incrémente le chrono d'une facture donnée"
            ]
        ),
        new GetCollection(),
        new Put(),
        new Delete(),
        new Patch()
    ]
)]
#[ApiResource(
    uriTemplate: '/customers/{id}/invoices',
    uriVariables: [
        'id' => new Link(fromClass: Customer::class, fromProperty: 'invoices')
    ],
    operations: [ new GetCollection() ],
    normalizationContext: [
        'groups' => ['invoices_subresource']
    ],
)]
#[ApiFilter(OrderFilter::class, properties:["amount","sentAt"])]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoices_read','customers_read', 'invoices_subresource'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['invoices_read','customers_read', 'invoices_subresource'])]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['invoices_read','customers_read', 'invoices_subresource'])]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['invoices_read','customers_read', 'invoices_subresource'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invoices_read'])]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Groups(['invoices_read', 'invoices_subresource'])]
    private ?int $chrono = null;

    /**
     * Permet de récup le user à qui appartient finalement la facture
     *
     * @return User
     */
    #[Groups(['invoices_read','invoices_subresource'])]
    public function getUser(): User {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): static
    {
        $this->chrono = $chrono;

        return $this;
    }
}
