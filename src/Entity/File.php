<?php

namespace App\Entity;

use App\Repository\FileRepository;
use App\Service\FileUploader;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    #[ORM\Column(length: 255)]
    private ?string $alias = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    private function __construct()
    {
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
    }

    public static function newFile($user, $title, $size, $extension, $alias, $path): self
    {
        $file = new self();
        $file->creator = $user;
        $file->title = $title;
        $file->size = $size;
        $file->extension = $extension;
        $file->alias = $alias;
        $file->path = $path;

        return $file;
    }

}
