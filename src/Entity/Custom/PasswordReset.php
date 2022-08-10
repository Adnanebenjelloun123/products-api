<?php

namespace App\Entity\Custom;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     itemOperations={"get", "put"},
 *     collectionOperations={"get"},
 * )
 */
class PasswordReset
{
    /**
     * @var string
     * @ApiProperty (identifier=true)
     */
    public $token;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $passwordRepeated;
}