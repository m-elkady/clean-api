<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class RefreshRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Refresh token is required')]
    public ?string $refresh_token = null;
}
