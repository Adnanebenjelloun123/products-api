<?php

namespace App\Entity\Custom;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User;

/**
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 * )
 */
class EmailConfirmation
{
    /**
     * @var string
     * @ApiProperty (identifier=true)
     */
    public $token;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;
}