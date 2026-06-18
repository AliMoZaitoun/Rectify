<?php

namespace App\Exceptions;

use App\Exceptions\V1\EmailAlreadyVerifiedException;
use App\Exceptions\V1\InvalidPasswordException;
use App\Exceptions\V1\InvalidRefreshTokenException;
use App\Exceptions\V1\Order\OrderAlreadySubmittedException;
use App\Exceptions\V1\Engineer\Attendance\OutsideGeofenceException;
use App\Exceptions\V1\Engineer\Attendance\AlreadyCheckedInException;
use App\Exceptions\V1\Engineer\Attendance\BuildingProjectMismatchException;
use App\Exceptions\V1\Engineer\Attendance\DeviceMismatchException;
use App\Exceptions\V1\Engineer\Attendance\BuildingRequiredException;
use App\Exceptions\V1\Engineer\Attendance\InvalidCheckOutTimeException;
use App\Exceptions\V1\Engineer\Attendance\NotCheckedInYetException;
use App\Exceptions\V1\Engineer\Attendance\LowGpsAccuracyException;
use App\Exceptions\V1\Engineer\Attendance\MockLocationDetectedException;
use App\Exceptions\V1\Engineer\Attendance\OfflineSyncExpiredException;
use App\Exceptions\V1\Engineer\Attendance\ShiftTimeoutException;
use App\Exceptions\V1\Engineer\Report\EngineerNotCheckedInException;
use App\Exceptions\V1\ProjectHasNoBuildingsException;
use App\Exceptions\V1\Sales\CompleteFutureAppointmentException;
use App\Traits\ResponseTrait;
use Exception;
use Gemini\Exceptions\TransporterException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\Permission\Exceptions\UnauthorizedException as UnauthorizedExceptionSpatie;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
    }
}
