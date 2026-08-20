<?php

return array(
    // Base URL of the pondokhuda API (without trailing slash).
    'base_url' => env('API_BASE_URL', 'https://api.pondokhuda.com/api'),
    // Shared API token for admin/owner/super-owner/report/keu/log endpoints.
    'token' => env('API_TOKEN', ''),
);