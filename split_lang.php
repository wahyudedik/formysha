<?php

/**
 * Script untuk memecah lang/id/app.php dan lang/en/app.php
 * menjadi file-file per group sesuai konvensi Laravel 11+.
 *
 * Laravel 11+ memuat translation dari lang/{locale}/{group}.php
 * bukan dari lang/{locale}/app.php dengan nested array.
 *
 * Jalankan: php split_lang.php
 */
$locales = ['id', 'en'];

foreach ($locales as $locale) {
    $appFile = __DIR__."/lang/{$locale}/app.php";

    if (! file_exists($appFile)) {
        echo "SKIP: {$appFile} not found\n";

        continue;
    }

    $groups = require $appFile;

    if (! is_array($groups)) {
        echo "SKIP: {$appFile} did not return an array\n";

        continue;
    }

    $langDir = __DIR__."/lang/{$locale}";

    foreach ($groups as $groupName => $groupData) {
        $filePath = "{$langDir}/{$groupName}.php";

        $content = "<?php\n\nreturn ".var_export($groupData, true).";\n";

        file_put_contents($filePath, $content);

        echo "Created: lang/{$locale}/{$groupName}.php\n";
    }

    echo "\nDone: {$locale} — ".count($groups)." files created\n\n";
}

echo "All done! Translation files split successfully.\n";
