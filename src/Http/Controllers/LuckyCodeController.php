<?php

namespace LuckyCode\IntegrationHelper\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use LuckyCode\IntegrationHelper\Services\Contracts\LuckyCodeServiceContract;
use LuckyCode\IntegrationHelper\Models\PullCodeRequest as PullCodeDto;
use LuckyCode\IntegrationHelper\Models\RevealCodeRequest as RevealCodeDto;
use LuckyCode\IntegrationHelper\Models\RedeemCodeRequest as RedeemCodeDto;
use LuckyCode\IntegrationHelper\Models\CustomerPakageLogQuery as CustomerPakageLogQueryDto;

class LuckyCodeController extends Controller
{
    public function __construct(private LuckyCodeService $service)
    {
    }

    public function pullCode(Request $request)
    {
        $dto = new PullCodeDto($request->all());
        return response()->json($this->service->pullCode($dto));
    }

    public function revealCode(Request $request)
    {
        $dto = new RevealCodeDto($request->all());
        return response()->json($this->service->revealCode($dto));
    }

    public function redeemCode(Request $request)
    {
        $dto = new RedeemCodeDto($request->all());
        return response()->json($this->service->redeemCode($dto));
    }

    public function multiPull(Request $request)
    {
        $dto = new PullCodeDto($request->all());
        return response()->json($this->service->multiPull($dto));
    }

    public function checkSerialCode(Request $request)
    {
        $serial = (string) $request->query('serialCode', '');
        return response()->json($this->service->checkSerialCode($serial));
    }

    public function getCustomersLog(Request $request)
    {
        $dto = new CustomerPakageLogQueryDto($request->all());
        return response()->json($this->service->getCustomersLog($dto));
    }
}

