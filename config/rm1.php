<?php

declare(strict_types=1);

return [
    // Pagination
    'items_per_page' => env('ITEMS_PER_PAGE', 15),

    // Contract expiry warning window (days)
    'contract_expiry_warning_days' => env('CONTRACT_EXPIRY_DAYS', 30),
];
