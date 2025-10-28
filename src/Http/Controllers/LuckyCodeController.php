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
    public function __construct(private LuckyCodeServiceContract $service)
    {
    }

    /**
     * Normalize all request keys to lowercase (case-insensitive parameters)
     */
    private function normalizeRequestKeys(Request $request): array
    {
        $normalized = [];
        foreach ($request->all() as $key => $value) {
            $normalized[strtolower($key)] = $value;
        }
        return $normalized;
    }

    /**
     * Pull a single lucky code
     */
   
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
 
/**
 * Check serial code (case-insensitive for parameter name)
 */
public function checkSerialCode(Request $request)
{
    // Get all query parameters and normalize keys to lowercase
    $normalized = [];
    foreach ($request->query() as $key => $value) {
        $normalized[strtolower($key)] = $value;
    }

    // Support serialCode / SERIALCODE / Serialcode / serialcode
    $serialCode = $normalized['serialcode'] ?? '';

    return response()->json(
        $this->service->checkSerialCode((string) $serialCode)
    );
}


    /**
     * Get customer package log
     */
    public function getCustomersLog(Request $request)
    {
        // Normalize both query and body parameters (just in case)
        $input = $request->isMethod('GET') ? $request->query() : $request->all();
    
        // Convert all keys to lowercase
        $normalized = [];
        foreach ($input as $key => $value) {
            $normalized[strtolower($key)] = $value;
        }
    
        // Map lowercase keys to expected DTO fields
        $dto = new CustomerPakageLogQueryDto([
            'page' => $normalized['page'] ?? null,
            'pageSize' => $normalized['pagesize'] ?? null,
            'customerRef' => $normalized['customerref'] ?? null,
        ]);
    
        return response()->json($this->service->getCustomersLog($dto));
    }
    
}