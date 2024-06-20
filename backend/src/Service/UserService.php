<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\AddUserRequest;
use App\Request\GetUserRequest;
use App\Request\PaginateUserRequest;
use App\Request\UpdateUserRequest;
use App\Response\AddUserResponse;
use App\Response\BaseResponse;
use App\Response\DeleteUserResponse;
use App\Response\ErrorResponse;
use App\Response\GetUserResponse;
use App\Response\PaginateUserResponse;
use App\Response\UpdateUserResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly ValidatorInterface $validator,
        private readonly Serializer $serializer,
    ) {
    }

    public function add(AddUserRequest $request): BaseResponse
    {
        $errors = $request->getValidationErrors(
            $this->validator->validate($request)
        );

        if (!empty($errors)) {
            return new ErrorResponse("Wrong Data! Please fix the errors and try again.", 400, $errors);
        }

        $user = new User();
        $user->setData($request->getAttributesValues());
        $this->userRepository->add($user);

        return new AddUserResponse($user);
    }

    public function update(int $id, UpdateUserRequest $request): BaseResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return new ErrorResponse("User not fount!", 404);
        }

        $errors = $request->getValidationErrors($this->validator->validate($request));

        if (!empty($errors)) {
            return new ErrorResponse("Wrong Data! Please fix the errors and try again.", 400, $errors);
        }

        $user->setData($request->getAttributesValues());
        $this->userRepository->getEntityManager()->flush();

        return new UpdateUserResponse($user);
    }

    public function delete(int $id): BaseResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return new ErrorResponse("User not found.", 404);
        }

        $this->userRepository->remove($user);
        return new DeleteUserResponse();
    }

    public function get(GetUserRequest $request): BaseResponse
    {
        if (empty($request->getAttributesValues())) {
            return new ErrorResponse("Wrong Data! Please fix the errors and try again.", 400);
        }

        $user = $this->userRepository->findOneBy($request->getAttributesValues());

        if (!$user) {
            return new ErrorResponse("User not found.", 404);
        }

        return new GetUserResponse($user);
    }

    public function paginate(PaginateUserRequest $request): BaseResponse
    {
        $errors = $request->getValidationErrors(
            $this->validator->validate($request)
        );

        if (!empty($errors)) {
            return new ErrorResponse("Wrong Data! Please fix the errors and try again.", 400, $errors);
        }

        $paginator = $this->userRepository->findAll($request->getAttributes(), $request->getQueryOptions());
        
        return new PaginateUserResponse($this->serializer, $paginator, $request->page, $request->perPage);
    }
}
