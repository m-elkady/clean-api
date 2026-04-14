<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $data = null,
        public readonly mixed $errors = null,
        public readonly string $message = '',
        public readonly int $code = 200
    ) {
    }

     public static function success(mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public static function created(mixed $data = null): JsonResponse
    {
        return self::success($data, Response::HTTP_CREATED);
    }

    public static function noContent(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => null,
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array<string, string> $errors
     */
    public static function validationError(array $errors): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function error(string $message, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'errors' => ['message' => $message],
        ], $status);
    }

    public static function notFound(string $message): JsonResponse
    {
        return self::error($message, Response::HTTP_NOT_FOUND);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, Response::HTTP_UNAUTHORIZED);
    }

    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'code' => $this->code,
        ];

        if ($this->message !== '') {
            $response['message'] = $this->message;
        }

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        return $response;
    }
}
