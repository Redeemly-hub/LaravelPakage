<?php

namespace LuckyCode\IntegrationHelper\Services\Contracts;

use LuckyCode\IntegrationHelper\Support\ApiResponse;
use LuckyCode\IntegrationHelper\Support\PaginatedList;
use LuckyCode\IntegrationHelper\Models\TokenModel;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest;
use LuckyCode\IntegrationHelper\Models\PullCodeResponse;
use LuckyCode\IntegrationHelper\Models\RevealCodeRequest;
use LuckyCode\IntegrationHelper\Models\RevealCodeResponse;
use LuckyCode\IntegrationHelper\Models\RedeemCodeRequest;
use LuckyCode\IntegrationHelper\Models\RedeemCodeResponse;
use LuckyCode\IntegrationHelper\Models\CheckSerialCodeModel;
use LuckyCode\IntegrationHelper\Models\CustomerLogModel;
use LuckyCode\IntegrationHelper\Models\CustomerPakageLogQuery;

interface LuckyCodeServiceContract
{
    public function getTokenWithCredential(array $credential): ApiResponse;
    public function getToken(): ApiResponse;
    public function pullCode(PullCodeRequest $dto): ApiResponse;
    public function revealCode(RevealCodeRequest $dto): ApiResponse;
    public function redeemCode(RedeemCodeRequest $dto): ApiResponse;
    public function multiPull(PullCodeRequest $dto): ApiResponse;
    public function checkSerialCode(string $serialCode): ApiResponse;
    public function getCustomersLog(CustomerPakageLogQuery $query): ApiResponse;
    public function ensureValidToken(): string;
}

