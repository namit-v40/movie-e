<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiResponse
{
    private bool $success;

    private string $message;

    private mixed $data;

    private ?array $errors;

    private int $status;

    public function __construct(bool $success, string $message, mixed $data = null, int $status = Response::HTTP_OK, ?array $errors = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
        $this->status = $status;
    }

    public function toJson(): JsonResponse
    {
        return response()->json([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ], $this->status);
    }

    public static function success(mixed $data, string $message = 'Success', int $status = Response::HTTP_OK): self
    {
        return new self(true, $message, $data, $status);
    }

    public static function error(string $message, int $status = Response::HTTP_BAD_REQUEST, ?array $errors = null): self
    {
        return new self(false, $message, null, $status, $errors);
    }
}
