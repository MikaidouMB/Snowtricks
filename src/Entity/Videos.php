<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\VideosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideosRepository::class)
 */
class Videos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id =null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    private $link;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="videos")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Trick $trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     * @return Videos
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }


    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
