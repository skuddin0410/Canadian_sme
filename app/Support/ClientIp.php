<?php

namespace App\Support;

use Illuminate\Http\Request;

class ClientIp
{
    public static function resolve(?Request $request = null): ?string
    {
        $request ??= request();

        if (!$request instanceof Request) {
            return null;
        }

        $candidates = array_merge(
            self::fromSingleHeader($request, 'CF-Connecting-IP'),
            self::fromSingleHeader($request, 'True-Client-IP'),
            self::fromSingleHeader($request, 'X-Real-IP'),
            self::fromForwardedFor($request),
            self::fromForwardedHeader($request),
            self::fromServer($request, 'REMOTE_ADDR')
        );

        foreach ($candidates as $candidate) {
            if (self::isPublicIp($candidate)) {
                return $candidate;
            }
        }

        foreach ($candidates as $candidate) {
            if (self::isValidIp($candidate)) {
                return $candidate;
            }
        }

        return self::normalize($request->ip());
    }

    private static function fromSingleHeader(Request $request, string $header): array
    {
        $value = $request->headers->get($header);

        if (!$value) {
            return [];
        }

        $ip = self::normalize($value);

        return $ip ? [$ip] : [];
    }

    private static function fromForwardedFor(Request $request): array
    {
        $value = $request->headers->get('X-Forwarded-For');

        if (!$value) {
            return [];
        }

        $ips = [];

        foreach (explode(',', $value) as $part) {
            $ip = self::normalize($part);

            if ($ip) {
                $ips[] = $ip;
            }
        }

        return $ips;
    }

    private static function fromForwardedHeader(Request $request): array
    {
        $value = $request->headers->get('Forwarded');

        if (!$value) {
            return [];
        }

        preg_match_all('/for=(?:"?\\[?)([^;,"\\]]+)/i', $value, $matches);

        $ips = [];

        foreach ($matches[1] ?? [] as $match) {
            $ip = self::normalize($match);

            if ($ip) {
                $ips[] = $ip;
            }
        }

        return $ips;
    }

    private static function fromServer(Request $request, string $key): array
    {
        $value = $request->server->get($key);

        if (!$value) {
            return [];
        }

        $ip = self::normalize($value);

        return $ip ? [$ip] : [];
    }

    private static function normalize(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($value === '' || strtolower($value) === 'unknown') {
            return null;
        }

        if (str_starts_with(strtolower($value), 'for=')) {
            $value = substr($value, 4);
        }

        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (str_starts_with($value, '[') && str_contains($value, ']')) {
            $end = strpos($value, ']');

            return substr($value, 1, $end - 1);
        }

        if (substr_count($value, ':') === 1 && str_contains($value, '.')) {
            [$host] = explode(':', $value, 2);
            $value = $host;
        }

        return filter_var($value, FILTER_VALIDATE_IP) ? $value : null;
    }

    private static function isPublicIp(?string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    private static function isValidIp(?string $ip): bool
    {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP);
    }
}
