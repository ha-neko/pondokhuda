<?php

if (!function_exists('api_header')) {
    /**
     * HTTP header block (with auth token) for talking to the pondokhuda API.
     *
     * @return string
     */
    function api_header()
    {
        $header = "Content-type: application/x-www-form-urlencoded\r\n";
        $token = config('api.token');
        if ($token !== '') {
            $header .= 'X-Api-Token: ' . $token . "\r\n";
        }
        return $header;
    }
}

if (!function_exists('api_url')) {
    /**
     * Build a pondokhuda API endpoint URL from the configured base.
     *
     * @param  string  $endpoint  Endpoint filename, e.g. 'login_ph.php'
     * @return string
     */
    function api_url($endpoint)
    {
        return rtrim(config('api.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }
}

if (!function_exists('api_get')) {
    /**
     * GET a pondokhuda API URL with the proper token header attached.
     *
     * @param  string  $url
     * @return string|false
     */
    function api_get($url)
    {
        $context = stream_context_create(array(
            'http' => array(
                'header' => api_header(),
                'method' => 'GET',
            ),
        ));
        return file_get_contents($url, false, $context);
    }
}