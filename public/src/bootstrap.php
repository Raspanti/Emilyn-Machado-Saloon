<?php
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/fotos-servicos.php';

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function renderIcon(string $name, string $class = ''): string
{
    $paths = [
        'hair' => '<circle cx="7" cy="17" r="3"/><circle cx="17" cy="17" r="3"/><path d="m9 15 9-12M15 15 6 3M12 11l-1 2"/>',
        'eye' => '<path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path d="M5 4c4-2 10-2 14 0"/>',
        'nails' => '<path d="M5 19V9a3 3 0 0 1 6 0v10M11 19V6a3 3 0 0 1 6 0v13M17 19v-7a2 2 0 0 1 4 0v7M3 21h18M5 12h6M11 10h6M17 15h4"/>',
        'leaf' => '<path d="M19 3C9 2 3 6 4 13c1 7 11 8 14 1 2-4 1-8 1-11ZM5 20 16 8M9 15v-5M12 12h5"/>',
        'spark' => '<path d="m12 3 2.5 6.5L21 12l-6.5 2.5L12 21l-2.5-6.5L3 12l6.5-2.5L12 3ZM20 2v4M18 4h4"/>',
        'bottle' => '<path d="M9 3h6v5l3 3v10H6V11l3-3V3ZM9 6h6M6 13h12M9 17h6"/>',
        'home' => '<path d="m3 11 9-8 9 8M5 10v11h5v-7h4v7h5V10"/>',
        'gallery' => '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 5-5 4 4 4-6 5 7"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M8 15h2M14 15h2M8 18h2"/>',
        'arrow' => '<path d="M12 20V4m-7 7 7-7 7 7"/>',
        'right' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'back' => '<path d="M20 12H4m6-6-6 6 6 6"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'pin' => '<path d="M19 10c0 5-7 12-7 12S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2"/>',
    ];
    return '<svg class="icon ' . escapeHtml($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['spark']) . '</svg>';
}

// Sem dados inventados: preencha os canais confirmados em src/site.php.
$site = require __DIR__ . '/site.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
