<?php

namespace App\Controller\Api;

use App\Service\Tools\HttpRequestService;
use App\Service\Tools\SerializerService;
use App\Service\User\UserService;
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

    public function __construct(
        HttpRequestService $httpRequestService,
        SerializerService $serializerService
    )
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
        return HttpRequestService::jsonResponseSuccess($message, $data);
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
        return HttpRequestService::jsonResponseFail($message, $data);
    }
}
