<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MessageFromClientDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 1024)]
    public string $message;

    #[Assert\File]
    public $file;
}