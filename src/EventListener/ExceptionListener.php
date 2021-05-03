<?php
namespace App\EventListener;

use App\Service\Tools\HttpRequestService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if($exception instanceof  HttpExceptionInterface) {
            $response = new JsonResponse();
            $data = [
                "message" => $exception->getMessage(),
                "status" => $this->getStatus($exception->getStatusCode()),
                "status_code" => $exception->getStatusCode()
            ];
            $response->setContent(json_encode($data));
            $response->setStatusCode($exception->getStatusCode());

//            $response->headers->replace($exception->getHeaders());
            $event->setResponse($response);
        }

    }

    private function getStatus(int $statusCode) {
        if ($statusCode > 400 || $statusCode < 500) {
            return "error";
        } elseif ($statusCode > 200 || $statusCode < 300) {
            return "success";
        } else {
            return $statusCode;
        }

    }
}