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
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array('Content-type: application/x-www-form-urlencoded', 'X-Api-Token: ' . config('api.token')),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result !== false ? $result : false;
    }
}

if (!function_exists('api_post')) {
    /**
     * POST to a pondokhuda API endpoint with token + form data.
     *
     * @param  string  $url
     * @param  array   $data  key => value pairs
     * @return string|false
     */
    function api_post($url, array $data)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => array('Content-type: application/x-www-form-urlencoded', 'X-Api-Token: ' . config('api.token')),
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result !== false ? $result : false;
    }
}