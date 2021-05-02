<?php

namespace App\Controller\Api;

use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Api Base Controller
 *
 * Class BaseController
 * @package App\Controller\Api
 */
class BaseController extends AbstractController
{
    protected SerializerService $serializerService;
    protected HttpRequestService $httpRequestService;

    public function __construct(HttpRequestService $httpRequestService,
                                SerializerService $serializerService)
    {
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
    }

    /**
     * Json success response function
     * Returns json response with 200 status, data array and message
     *
     * @param $message
     * @param array $data
     * @return JsonResponse
     */
    protected function jsonResponseSuccess($message, $data = [])
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
    protected function jsonResponseFail($message, $data = [])
    {
        $responseData = [
            // you may want to customize or obfuscate the message first
            'status' => "error",
            'message' => $message,
            "data" => $data
        ];

        return new JsonResponse($responseData, Response::HTTP_BAD_REQUEST);
    }
}
