<?php

namespace App\Helpers;

class ToastHelper
{
    public static function success($message, $duration = 5000)
    {
        session()->flash('toast', [
            'type' => 'success',
            'message' => $message,
            'duration' => $duration
        ]);
    }

    public static function error($message, $duration = 5000)
    {
        session()->flash('toast', [
            'type' => 'error',
            'message' => $message,
            'duration' => $duration
        ]);
    }

    public static function warning($message, $duration = 5000)
    {
        session()->flash('toast', [
            'type' => 'warning',
            'message' => $message,
            'duration' => $duration
        ]);
    }

    public static function info($message, $duration = 5000)
    {
        session()->flash('toast', [
            'type' => 'info',
            'message' => $message,
            'duration' => $duration
        ]);
    }
}