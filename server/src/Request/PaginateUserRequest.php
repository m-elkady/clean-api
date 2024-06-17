<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PaginateUserRequest extends BaseRequest
{
    public ?int $start = 0;
    public ?int $perPage = 20;
    #[Assert\Choice(
        choices: ['id', 'firstName', 'lastName', 'userEmail'],
        message: 'Invalid sort by field',
    )]
    public ?string $sortBy = 'id';
    public ?string $order = 'asc';

}