<?php

namespace App\Request;

use App\Service\Constants;
use Symfony\Component\Validator\Constraints as Assert;

class PaginateUserRequest extends BaseRequest implements RequestValidatedInterface
{
    public ?int $page = 1;
    public ?int $perPage = Constants::PAGE_LIMIT;

    #[Assert\Choice(
        choices: ['id', 'firstName', 'lastName', 'email'],
        message: 'Invalid sort by field',
    )]
    public ?string $sortBy = 'id';
    public ?string $order = 'asc';
    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;

    public $queryParams = ['firstName', 'lastName', 'email'];


    public static function fromArray(array $data): self
    {
        $request = new self();
        $request->page = $data['page'] ?? 1;
        $request->perPage = $data['perPage'] ?? $request->perPage;
        $request->sortBy = $data['sortBy'] ?? 'id';
        $request->order = $data['order'] ?? 'asc';
        $request->firstName = $data['firstName'] ?? null;
        $request->lastName = $data['lastName'] ?? null;
        $request->email = $data['email'] ?? null;

        return $request;
    }

    public function getQueryOptions(): array
    {
        $queryParams = [];
        foreach ($this->queryParams as $key) {
            if (!empty($this->$key)) {
                $queryParams[$key] = $this->$key;
            }
        }
        return $queryParams;
    }
}
