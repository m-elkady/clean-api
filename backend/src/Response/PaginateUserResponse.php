<?php

namespace App\Response;

use App\Entity\User;
use App\Service\Constants;
use App\Service\Serializer;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginateUserResponse extends BaseResponse
{
    public array $users;
    public int $count;

    public function __construct(
        private readonly ?Serializer $serializer,
        Paginator $paginator,
        public int $currentPage,
        public int $limit = Constants::PAGE_LIMIT
    ) {
        $this->count = count($paginator);
        $this->users = $this->serializer->denormalize(iterator_to_array($paginator), User::class . '[]');
        
        parent::__construct();
    }
}
