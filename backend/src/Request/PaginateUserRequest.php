<?php

namespace App\Request;

use App\Service\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaginateUserRequest extends BaseRequest
{
    public ?int $page = 1;
    public ?int $perPage = Constants::PAGE_LIMIT;
    #[Assert\Choice(
        choices: ['id', 'firstName', 'lastName', 'userEmail'],
        message: 'Invalid sort by field',
    )]
    public ?string $sortBy = 'id';
    public ?string $order = 'asc';

    public ?string $firstName;
    public ?string $lastName;
    public ?string $userEmail;

    public $queryParams = ['firstName', 'lastName', 'userEmail'];

    public static function fromRequest(Request $request, SerializerInterface $serializer)
    {
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->get('perPage', Constants::PAGE_LIMIT);
        $sortBy = $request->query->get('sortBy', 'id');
        $order = $request->query->get('order', 'asc');

        $firstName = $request->query->get('firstName');
        $lastName = $request->query->get('lastName');
        $userEmail = $request->query->get('userEmail');


        $paginateOptions = ['page' => $page, 'perPage' => $perPage, 'sortBy' => $sortBy, 'order' => $order];
        $queryOptions = ['firstName' => $firstName, 'lastName' => $lastName, 'userEmail' => $userEmail];

        $data = json_encode(array_merge($paginateOptions, $queryOptions));

        return $serializer->deserialize($data, self::class, 'json');
    }

    public function getQueryOptions(): array
    {
        $queryParams = [];
        foreach ($this->getAttributesValues() as $key => $value) {
            if (in_array($key, $this->queryParams)) {
                $queryParams[$key] = $value;
            }
        }
        return $queryParams;
    }
}
