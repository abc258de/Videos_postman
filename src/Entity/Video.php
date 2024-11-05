<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Traits\Timestampable;

use Symfony\Component\Validator\Constraints as Assert; 
use App\Validator\InappropriateWords;

// use Symfony\Component\HttpFoundation\File\File;
// use Vich\UploaderBundle\Mapping\Annotation as Vich; 


use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: "videos")] 

// #[Vich\Uploadable]
#[ApiResource]

#[ORM\HasLifecycleCallbacks]

class Video
{

    #[ORM\PrePersist]
    #[ORM\PreUpdate] 
    public function updateTimestamps()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable);
        }
        $this->setUpdateAt(new \DateTimeImmutable);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 90)]
    #[InappropriateWords()]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide")]
    #[Assert\Length(min:3, minMessage: 'The title must be at least {{ limit }} characters long.')]
    private ?string $title = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide")]
    private ?string $videoLink = null;

    #[ORM\Column(type: Types::TEXT)]
    #[InappropriateWords()]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide")]
    #[Assert\Length(min:20, minMessage: 'The description must be at least {{ limit }} characters long.')]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;




    // #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'imageName', size: 'imageSize')]
    // private ?File $imageFile = null;

    // #[ORM\Column(nullable: true)]
    // private ?string $imageName = 'default_profile.jpg';

    // #[ORM\Column(nullable: true)]
    // private ?int $imageSize = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVideoLink(): ?string
    {
        return $this->videoLink;
    }

    public function setVideoLink(string $videoLink): static
    {
        $this->videoLink = $videoLink;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    

}
