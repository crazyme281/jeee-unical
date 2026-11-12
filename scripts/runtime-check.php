<?php

$required = ['curl', 'fileinfo', 'gd', 'mbstring', 'openssl', 'pdo_mysql', 'pdo_pgsql', 'zip'];
$missing = array_values(array_filter($required, fn ($name) => !extension_loaded($name)));
$oldWindowsBuild = PHP_OS_FAMILY === 'Windows'
    && PHP_VERSION === '8.5.12'
    && strtotime(PHP_BUILD_DATE) < strtotime('2026-10-26');
$wrongBuildType = PHP_OS_FAMILY === 'Windows' && !PHP_ZTS;
echo json_encode([
    'version' => PHP_VERSION,
    'build' => PHP_BUILD_DATE,
    'file' => PHP_BINARY,
    'ini' => php_ini_loaded_file(),
    'upload_progress' => ini_get('session.upload_progress.enabled'),
    'missing' => $missing,
    'old_windows_build' => $oldWindowsBuild,
    'wrong_build_type' => $wrongBuildType,
], JSON_PRETTY_PRINT).PHP_EOL;
exit(!str_starts_with(PHP_VERSION, '8.5.') || version_compare(PHP_VERSION, '8.5.12', '<') || $oldWindowsBuild || $wrongBuildType || $missing || ini_get('session.upload_progress.enabled') ? 1 : 0);
