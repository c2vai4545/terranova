<?php

class RateLimiter
{
    private static array $requests = [];
    private static int $limit = 5; // Max requests
    private static int $window = 60; // Time window in seconds

    public static function check(string $key): bool
    {
        $now = time();
        // Clean up old requests
        if (isset(self::$requests[$key])) {
            self::$requests[$key] = array_filter(self::$requests[$key], function ($timestamp) use ($now) {
                return $timestamp > $now - self::$window;
            });
        }

        // Check if limit is exceeded
        if (isset(self::$requests[$key]) && count(self::$requests[$key]) >= self::$limit) {
            return false;
        }

        return true;
    }

    public static function record(string $key): void
    {
        self::$requests[$key][] = time();
    }

    public static function setLimit(int $limit): void
    {
        self::$limit = $limit;
    }

    public static function setWindow(int $window): void
    {
        self::$window = $window;
    }

    public static function getRemainingAttempts(string $key): int
    {
        $now = time();
        if (isset(self::$requests[$key])) {
            self::$requests[$key] = array_filter(self::$requests[$key], function ($timestamp) use ($now) {
                return $timestamp > $now - self::$window;
            });
            return max(0, self::$limit - count(self::$requests[$key]));
        }
        return self::$limit;
    }

    public static function getRetryAfter(string $key): int
    {
        if (isset(self::$requests[$key]) && count(self::$requests[$key]) >= self::$limit) {
            $oldestRequest = min(self::$requests[$key]);
            return ($oldestRequest + self::$window) - time();
        }
        return 0;
    }

    public static function clear(string $key): void
    {
        unset(self::$requests[$key]);
    }
}