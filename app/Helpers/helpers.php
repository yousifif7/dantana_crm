<?php

if (!function_exists('format_currency')) {
    function format_currency(float $amount, string $currency = 'NGN'): string
    {
        $symbols = [
            'NGN' => '₦',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        return $symbol . number_format($amount, 2);
    }
}

if (!function_exists('get_status_badge_class')) {
    function get_status_badge_class(string $status): string
    {
        return match($status) {
            'approved', 'completed', 'active', 'in_stock', 'present' => 'success',
            'pending', 'in_progress', 'on_order' => 'warning',
            'rejected', 'cancelled', 'inactive', 'low_stock', 'absent' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('calculate_working_days')) {
    function calculate_working_days(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);
        
        $workingDays = 0;
        foreach ($dateRange as $date) {
            $dayOfWeek = $date->format('N');
            if ($dayOfWeek < 6) { // Monday to Friday
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
}

if (!function_exists('get_user_initials')) {
    function get_user_initials(string $firstName, string $lastName): string
    {
        return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    }
}

if (!function_exists('mask_email')) {
    function mask_email(string $email): string
    {
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1] ?? '';
        
        $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
        return $maskedUsername . '@' . $domain;
    }
}

if (!function_exists('generate_random_color')) {
    function generate_random_color(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}

if (!function_exists('get_priority_color')) {
    function get_priority_color(int $priority): string
    {
        return match($priority) {
            1 => '#dc3545', // Critical - Red
            2 => '#fd7e14', // High - Orange
            3 => '#ffc107', // Medium - Yellow
            4 => '#28a745', // Low - Green
            5 => '#6c757d', // Very Low - Gray
            default => '#007bff', // Default - Blue
        };
    }
}

if (!function_exists('sanitize_filename')) {
    function sanitize_filename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return substr($filename, 0, 255);
    }
}

if (!function_exists('calculate_percentage')) {
    function calculate_percentage(float $value, float $total): float
    {
        if ($total == 0) {
            return 0;
        }
        return round(($value / $total) * 100, 2);
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

