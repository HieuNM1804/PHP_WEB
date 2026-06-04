<?php
/**
 * Helper Functions
 */

/**
 * Flash message helpers
 */
function setFlash(string $type, string $message): void
{
    $_SESSION["flash_{$type}"] = $message;
}

function getFlash(string $type): string
{
    $msg = $_SESSION["flash_{$type}"] ?? '';
    unset($_SESSION["flash_{$type}"]);
    return $msg;
}

function renderFlash(): string
{
    $html = '';
    $success = getFlash('success');
    $error = getFlash('error');

    if ($success) {
        $html .= '<div class="alert alert-success">';
        $html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>';
        $html .= '<span>' . htmlspecialchars($success) . '</span>';
        $html .= '<button class="alert-close" onclick="this.parentElement.remove()">&times;</button>';
        $html .= '</div>';
    }
    if ($error) {
        $html .= '<div class="alert alert-error">';
        $html .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
        $html .= '<span>' . htmlspecialchars($error) . '</span>';
        $html .= '<button class="alert-close" onclick="this.parentElement.remove()">&times;</button>';
        $html .= '</div>';
    }
    return $html;
}

/**
 * Format tiền VNĐ
 */
function formatPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

/**
 * Escape HTML output
 */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Base URL
 */
function appBaseUrl(): string
{
    $baseUrl = getenv('APP_BASE_URL');

    if ($baseUrl === false || $baseUrl === '') {
        $baseUrl = '/gs25/';
    }

    $baseUrl = '/' . trim($baseUrl, '/') . '/';

    return $baseUrl === '//' ? '/' : $baseUrl;
}

function baseUrl(string $path = ''): string
{
    return appBaseUrl() . ltrim($path, '/');
}
