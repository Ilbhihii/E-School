<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class SafeEducationalUpload implements Rule
{
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'cgi', 'pl', 'py', 'sh', 'bash', 'zsh',
        'bat', 'cmd', 'com', 'exe', 'msi', 'dll',
        'ps1', 'vbs', 'scr',
        'js', 'mjs', 'cjs', 'html', 'htm', 'svg',
        'jar', 'apk', 'app', 'dmg', 'iso',
    ];

    public function passes($attribute, $value): bool
    {
        if (!$value instanceof UploadedFile) {
            return true;
        }

        $extension = mb_strtolower(
            trim((string) $value->getClientOriginalExtension())
        );

        if (
            $extension !== ''
            && in_array($extension, self::BLOCKED_EXTENSIONS, true)
        ) {
            return false;
        }

        $mime = mb_strtolower(
            trim((string) ($value->getMimeType() ?: ''))
        );

        $blockedMimes = [
            'text/html',
            'application/x-httpd-php',
            'application/x-php',
            'application/x-msdownload',
            'application/x-dosexec',
            'application/x-sh',
            'application/javascript',
            'text/javascript',
        ];

        return !in_array($mime, $blockedMimes, true);
    }

    public function message(): string
    {
        return 'Ce type de fichier n’est pas autorisé pour des raisons de sécurité.';
    }
}
