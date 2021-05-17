<?php
namespace App\Service\Tools;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HttpRequestService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Json success response function
     * Returns json response with 200 status, data array and message
     *
     * @param $message
     * @param array $data
     * @return JsonResponse
     */
    public static function jsonResponseSuccess($message, $data = [])
    {
        $responseData = [
            // you may want to customize or obfuscate the message first
            'status' => "success",
            'message' => $message,
            "data" => $data
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);
    }

    /**
     * Json fail response function
     * Returns json response with 400 status, data array and message
     *
     * @param $message
     * @param array $data
     * @return JsonResponse
     */
    public static function jsonResponseFail($message, $data = [])
    {
        $responseData = [
            // you may want to customize or obfuscate the message first
            'status' => "error",
            'message' => $message,
            "data" => $data
        ];

        return new JsonResponse($responseData, Response::HTTP_BAD_REQUEST);
    }

    public static function validateRequestData(string $method, Request $request, array $requiredFields = []): JsonResponse|array
    {
        $requestData = match ($method) {
            "get", "GET" => $request->query->all(),
            default => HttpRequestService::getRequestData($request, true),
        };
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $requestData)) {
                return self::jsonResponseFail(
                    sprintf("Required field [%s] not in request.", $field)
                );
            }
        }
        return $requestData;
    }

    public static function getRequestData(Request $request, $array = false) {
        if ($request->getContentType() == "json") {
            return json_decode($request->getContent(), $array);
        }
        return $request->request->all();
    }

    public function validateData($entity) {
        $errors = $this->validator->validate($entity);

        if (count($errors) === 0) {
            return true;
        }
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = sprintf("Field: (%s) - %s", $error->getPropertyPath(), $error->getMessage());
        }
        throw new BadRequestHttpException("Validation failed. " . implode(",", $errorMessages));
    }
}