<?php

namespace LuckyCode\IntegrationHelper\Models;

class CustomerPakageLogQuery
{
    public function __construct(array $data = [])
    {
        $this->page = (int) ($data['page'] ?? 1);
        $this->pageSize = (int) ($data['pageSize'] ?? 10);
        $this->customerRef = $data['customerRef'] ?? null;
    }

    public int $page = 1;
    public int $pageSize = 10;
    public ?string $customerRef = null;
}

