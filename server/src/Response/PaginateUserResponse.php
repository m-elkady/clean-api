<?php

namespace App\Response;

use App\Entity\User;
use App\Service\Serializer;

class PaginateUserResponse extends BaseResponse
{
    public function __construct(
        private readonly ?Serializer $serializer,
        public array                 $users,
    )
    {
        $this->users = $this->serializer->denormalize($users, User::class . '[]');
        parent::__construct();
    }
}