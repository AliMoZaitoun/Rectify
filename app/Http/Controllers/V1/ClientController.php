<?php

namespace App\Http\Controllers\V1;

use App\DTOs\Client\Create\CreateClientDTO;
use App\DTOs\User\Create\CreateUserDTO;
use App\DTOs\Client\Update\UpdateClientDTO;
use App\DTOs\User\Update\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Client\ClientRequest;
use App\Http\Resources\V1\ClientDetailResource;
use App\Services\Client\ClientService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ClientService $clientService
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage');
        $clients = $this->clientService->index(perPage: $perPage);
        return $this->successUserCollection($clients);
    }

    public function store(ClientRequest $clientRequest)
    {
        $userDTO = CreateUserDTO::fromRequest($clientRequest->validated(), 'client');

        $clientDTO = CreateClientDTO::fromRequest($clientRequest->validated());

        $data = $this->clientService->store($userDTO, $clientDTO);

        return $this->successResponse([], __('messages.auth.otp_sent'), 201);

        // return $this->useResource($data['client'], ClientDetailResource::class, __('messages.auth.otp_sent'), 201);
    }

    public function show(int $id)
    {
        $client = $this->clientService->show($id);
        return $this->useResource($client, ClientDetailResource::class);
    }

    public function profile()
    {
        $client = $this->clientService->profile();
        return $this->useResource($client, ClientDetailResource::class);
    }

    public function update(Request $request, int $id)
    {
        $userDTO = UpdateUserDTO::fromRequest($request->all());

        $clientDTO = UpdateClientDTO::fromRequest($request->all());

        $client = $this->clientService->update($id, $userDTO, $clientDTO);

        return $this->useResource($client, ClientDetailResource::class, __('messages.common.updated'));
    }

    public function destroy(int $id)
    {
        $this->clientService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
