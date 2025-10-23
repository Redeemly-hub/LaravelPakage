<?php

namespace LuckyCode\IntegrationHelper\Support;

class PaginatedList
{
    public function __construct(
        public array $items,
        public int $page,
        public int $pageSize,
        public int $total
    ) {
    }
}

