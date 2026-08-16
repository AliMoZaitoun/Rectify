<?php

namespace App\Services\Client;

use App\DAO\Client\ClientDAO;
use App\DAO\UserDAO;
use App\DTOs\Client\Update\UpdateClientDTO;
use App\DTOs\User\Update\UpdateUserDTO;
use App\DTOs\Client\Create\CreateClientDTO;
use App\DTOs\User\Create\CreateUserDTO;
use App\Events\OTPEvent;
use App\Events\V1\Client\PointsRedeemedEvent;
use App\Exceptions\NotFoundException;
use App\Exceptions\V1\Client\ClientNotFoundException;
use App\Exceptions\V1\Client\InsufficientPointsException;
use App\Models\Core\Employee;
use App\Services\OtpService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function __construct(
        private ClientDAO $clientDAO,
        private UserDAO $userDAO,
        private OtpService $otpService,
        private TransactionService $transaction
    ) {}

    public function index(array $relations = [], int $perPage = 15)
    {
        return $this->clientDAO->index($relations, $perPage);
    }

    public function store(CreateUserDTO $userDTO, CreateClientDTO $clientDTO)
    {
        return $this->transaction->execute(function () use ($userDTO, $clientDTO) {
            $user = $this->userDAO->store($userDTO);
            $clientDTO->user_id = $user->id;

            $client = $this->clientDAO->store($clientDTO);

            $otp = $this->otpService->createCode($user->id);

            event(new OTPEvent($otp, $user->email));

            return $client;
        });
    }

    public function show(int $id)
    {
        return $this->clientDAO->show($id) ?? throw new NotFoundException('Client');
    }

    public function profile()
    {
        $user = Auth::user();
        return $this->clientDAO->show($user->client->id);
    }

    public function update(int $id, UpdateUserDTO $userDTO, UpdateClientDTO $clientDTO)
    {
        return $this->transaction->execute(function () use ($id, $userDTO, $clientDTO) {
            $client = $this->show($id);
            $this->userDAO->update($client->user->id, $userDTO);
            $this->clientDAO->update($id, $clientDTO);
            $client->refresh();
            return $client;
        });
    }

    public function destroy(int $id)
    {
        return $this->clientDAO->destroy($id);
    }

    public function redeemPoints(int $clientId, int $pointsToRedeem, int $employeeId)
    {
        return $this->transaction->execute(function () use ($clientId, $pointsToRedeem, $employeeId) {
            $client = $this->clientDAO->show($clientId);

            if (! $client) {
                throw new ClientNotFoundException();
            }

            if ($client->points < $pointsToRedeem) {
                throw new InsufficientPointsException();
            }

            $this->clientDAO->decrementPoints($clientId, $pointsToRedeem);

            $employee = Employee::with('user')->find($employeeId);

            event(new PointsRedeemedEvent($client, $pointsToRedeem, $employee));

            return $client->fresh();
        });
    }
}
