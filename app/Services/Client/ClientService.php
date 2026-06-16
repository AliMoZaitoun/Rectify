<?php

namespace App\Services\Client;

use App\DAO\Client\ClientDAO;
use App\DAO\UserDAO;
use App\DTOs\Client\Update\UpdateClientDTO;
use App\DTOs\User\Update\UpdateUserDTO;
use App\DTOs\Client\Create\CreateClientDTO;
use App\DTOs\User\Create\CreateUserDTO;
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

    public function index()
    {
        return $this->clientDAO->index();
    }

    public function store(CreateUserDTO $userDTO, CreateClientDTO $clientDTO)
    {
        return $this->transaction->execute(function () use ($userDTO, $clientDTO) {
            $user = $this->userDAO->store($userDTO);
            $clientDTO->user_id = $user->id;

            $client = $this->clientDAO->store($clientDTO);

            $code = $this->otpService->createCode($user->id);
            // event(new OTPEvent($code, $user->email));

            // For Testing
            return ['client' => $client, 'otp' => $code];
        });
    }

    public function show(int $id)
    {
        return $this->clientDAO->show($id);
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
}
