<?php

namespace app\helpers;

class TinyMCEHelper
{
    public static function render(string $selector, array $options = []): string
    {
        $defaultOptions = [
            'selector' => $selector,
            'license_key' => 'gpl',
            'plugins' => 'lists link table code preview fullscreen searchreplace wordcount',
            'toolbar' => 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | preview fullscreen code',
            'menubar' => false,
            'statusbar' => true,
            'height' => 420,
            'skin' => 'oxide-dark',
            'content_css' => 'dark',
            'branding' => false,
            'promotion' => false,
            'browser_spellcheck' => true,
        ];

        $mergedOptions = array_merge($defaultOptions, $options);
        $jsonOptions = json_encode(
            $mergedOptions,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
        $scriptUrl = htmlspecialchars(
            URL_BASE . '/assets/tinymce_8.10.1/tinymce/js/tinymce/tinymce.min.js',
            ENT_QUOTES,
            'UTF-8'
        );

        return <<<HTML
<script src="{$scriptUrl}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        tinymce.init({$jsonOptions});
    });
</script>
HTML;
    }
}
