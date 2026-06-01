<?php

return [

    // Default / fallback locale (content slugs and fallbacks derive from this).
    'default' => 'ru',

    /*
     * Locales available on the platform.
     * key  => ISO code used by app()->setLocale() and translation files
     * native => label shown in the language switcher
     * flag => emoji shown in the switcher
     */
    'available' => [
        'ru' => ['native' => 'Русский',  'flag' => '🇷🇺'],
        'kk' => ['native' => 'Қазақша',  'flag' => '🇰🇿'],
        'en' => ['native' => 'English',  'flag' => '🇬🇧'],
    ],
];
