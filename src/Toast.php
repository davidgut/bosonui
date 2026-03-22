<?php

namespace DavidGut\Boson;

class Toast
{
    /**
     * Flash a plain toast to the session (no variant/icon).
     */
    public static function show(string $text, string|null $heading = null, int $duration = 5000): void
    {
        static::flash(null, $text, $heading, $duration);
    }

    /**
     * Flash a success toast to the session.
     */
    public static function success(string $text, string|null $heading = null, int $duration = 5000): void
    {
        static::flash('success', $text, $heading, $duration);
    }

    /**
     * Flash a warning toast to the session.
     */
    public static function warning(string $text, string|null $heading = null, int $duration = 5000): void
    {
        static::flash('warning', $text, $heading, $duration);
    }

    /**
     * Flash a danger toast to the session.
     */
    public static function danger(string $text, string|null $heading = null, int $duration = 5000): void
    {
        static::flash('danger', $text, $heading, $duration);
    }

    /**
     * Flash a toast to the session.
     */
    protected static function flash(string|null $variant, string $text, string|null $heading, int $duration): void
    {
        $toasts = session('boson_toasts', []);
        
        $toast = ['text' => $text];
        
        if ($variant !== null) {
            $toast['variant'] = $variant;
        }
        if ($heading !== null) {
            $toast['heading'] = $heading;
        }
        if ($duration !== 5000) {
            $toast['duration'] = $duration;
        }

        $toasts[] = $toast;

        session()->flash('boson_toasts', $toasts);
    }
}