<?php

namespace App\Response;

use App\Entity\User;

class UpdateUserResponse extends BaseResponse
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $userEmail;

    public function __construct(User $user)
    {
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->userEmail = $user->getUserEmail();

        parent::__construct(message: 'User Updated Successfully');
    }

}