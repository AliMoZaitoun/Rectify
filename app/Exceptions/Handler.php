<?php

namespace App\Exceptions;

use App\Exceptions\V1\AI\AiAssistantDisabledException;
use App\Exceptions\V1\AI\AiResponseGenerationException;
use App\Exceptions\V1\AI\AiConnectionException;
use App\Exceptions\V1\AI\AiParsingException;
use App\Exceptions\V1\AI\AiReportNotFoundException;
use App\Exceptions\V1\AI\NoComplaintsFoundForAnalysisException;
use App\Exceptions\V1\Complaint\CannotDeleteGrantedCompensationException;
use App\Exceptions\V1\Complaint\CannotReopenComplaintException;
use App\Exceptions\V1\Complaint\CompensationNotFoundException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyCompensatedException;
use App\Exceptions\V1\Complaint\ComplaintAlreadyRatedException;
use App\Exceptions\V1\Complaint\ComplaintNotFoundException;
use App\Exceptions\V1\Complaint\ComplaintNotResolvedForRatingException;
use App\Exceptions\V1\Complaint\DeviceIdRequiredException;
use App\Exceptions\V1\Complaint\UnresolvedComplaintCompensationException;
use App\Exceptions\V1\EmailAlreadyVerifiedException;
use App\Exceptions\V1\InvalidPasswordException;
use App\Exceptions\V1\InvalidRefreshTokenException;
use App\Traits\ResponseTrait;
use Gemini\Exceptions\TransporterException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Spatie\Permission\Exceptions\UnauthorizedException as UnauthorizedExceptionSpatie;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    use ResponseTrait;

    public function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (NotFoundHttpException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        });

        $exceptions->render(function (UnauthorizedException $e) {
            return $this->errorResponse(__('messages.common.unauthorized'), 401);
        });

        $exceptions->render(function (UnauthorizedExceptionSpatie $e) {
            return $this->errorResponse(__('messages.common.unauthorized'), 401);
        });

        $exceptions->render(function (AccessDeniedHttpException $e) {
            return $this->errorResponse(__('messages.common.unauthorized'), 403);
        });

        $exceptions->render(function (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        });

        $exceptions->render(function (NotFoundException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (InvalidCredentialException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 401);
        });

        $exceptions->render(function (OtpCodeInvalidException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 403);
        });

        $exceptions->render(function (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 403);
        });

        $exceptions->render(function (InvalidPasswordException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 422);
        });

        $exceptions->render(function (InvalidRefreshTokenException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 401);
        });

        $exceptions->render(function (EmailAlreadyVerifiedException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 400);
        });

        $exceptions->render(function (TransporterException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        });

        // Complaint Exceptions Handlers
        $exceptions->render(function (UnresolvedComplaintCompensationException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 422);
        });

        $exceptions->render(function (ComplaintAlreadyCompensatedException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 422);
        });

        $exceptions->render(function (CannotDeleteGrantedCompensationException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 422);
        });

        $exceptions->render(function (DeviceIdRequiredException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 422);
        });

        $exceptions->render(function (CompensationNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (ComplaintNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (ComplaintNotResolvedForRatingException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (ComplaintAlreadyRatedException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (CannotReopenComplaintException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        # AI 

        $exceptions->render(function (AiAssistantDisabledException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 403);
        });

        $exceptions->render(function (AiResponseGenerationException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        });

        $exceptions->render(function (AiConnectionException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 502);
        });

        $exceptions->render(function (AiParsingException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        });

        $exceptions->render(function (NoComplaintsFoundForAnalysisException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });

        $exceptions->render(function (AiReportNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 404);
        });
    }
}
