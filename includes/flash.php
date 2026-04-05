<?php

declare(strict_types=1);

function flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function flash_take(string $key): ?string
{
    if (empty($_SESSION['_flash'][$key])) {
        return null;
    }
    $msg = (string) $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);

    return $msg;
}
