<?php

namespace App\Traits;

use App\EnumsAndConsts\HttpStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Provide methods that determine how response should be generated.
 */
trait Extendable
{
    /**
     * Prepare the API response
     *
     * @param  array  $data
     * @return void
     */
    public function buildResponse($data = [], $extra_data = null)
    {
        $resp = $data['response_data'] ?? null;
        $errors = $data['errors'] ?? null;
        $token = $data['token'] ?? null;
        $error_code = $data['error_code'] ?? null;

        $info = [
            'api' => [
                'name' => 'PalmIP API',
                'version' => env('APP_VERSION', config('app.api.version.code', '1.0.0')),
                'author' => 'Greysoft Technologies',
                'updated' => env('LAST_UPDATE', '2022-09-20 02:27:53'),
            ],
            'message' => $data['message'] ?? 'Request was successful',
            'status' => $data['status'] ?? 'success',
            'status_code' => $data['status_code'] ?? HttpStatus::OK,
        ];

        $data = collect($data)->except(
            'message',
            'status_code',
            'error_code',
            'status',
            'errors',
            'token',
            'response_data'
        );

        $main_data = $data['data'] ?? $data ?? [];

        if (isset($main_data['data']['data']) && count($main_data['data']) === 1) {
            $main_data = $main_data['data']['data'] ?? [];
        }

        $response = collect($info);

        if ($extra_data) {
            if (is_array($extra_data)) {
                $response[key($extra_data)] = $extra_data[key($extra_data)];
            } else {
                $response['load'] = $extra_data;
            }
        }

        $response['data'] = $main_data;

        if ($resp) {
            $response['resp'] = $resp;
        }

        if ($error_code) {
            $response['error_code'] = intval($error_code);
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        if ($token) {
            $response['token'] = $token;
        }

        // DD( $info, $response);
        return response($response, $info['status_code']);
    }

    /**
     * Prepare the validation error.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function validatorFails(Validator $validator, $field = null)
    {
        return $this->buildResponse([
            'message' => $field ? $validator->errors()->first() : 'Your input has a few errors',
            'status' => 'error',
            'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
            'errors' => $validator->errors(),
        ]);
    }

    public function time()
    {
        return time();
    }

    /**
     * Check if this app is running on a local host
     *
     * @return bool
     */
    public function isLocalHosted(): bool
    {
        $ip = request()->ip();

        return stripos($ip, '127.0.0') !== false && env('APP_ENV') === 'local';
    }

    /**
     * Get the client IP address  or return preset IP if locally hosted
     *
     * @return void
     */
    public function ip()
    {
        $ip = request()->ip();
        if ($this->isLocalHosted()) {
            $ip = '197.210.76.68';
        }

        return $ip;
    }

    /**
     * Get the client's IP information
     *
     * @param [type] $key
     * @return void
     */
    public function ipInfo($key = null, $force = false)
    {
        $info['country'] = 'US';
        $user = Auth::user();

        if ($user?->access_data && !$force) {
            $info = $user->access_data;
        } else {
            if (config('settings.system.ipinfo.access_token') && config('settings.collect_user_data', true)) {
                $ipInfo = \Illuminate\Support\Facades\Http::get('ipinfo.io/' . $this->ip(), [
                    'token' => config('settings.system.ipinfo.access_token'),
                ]);

                if ($ipInfo->status() === 200) {
                    $info = $ipInfo->json() ?? $info;
                }
            }
        }

        return $key ? ($info[$key] ?? '') : $info;
    }

    public function uriQuerier(string|array $query): array
    {
        $parsed = [];
        if (is_array($query)) {
            $parsed = http_build_query($query);
        }

        parse_str($parsed, $output);

        return $output;
    }
}
