<?php
echo "<h1>🔍 Debug Information</h1>";
echo "<h2>Current Directory: " . __DIR__ . "</h2>";
echo "<h2>Files in this directory:</h2>";
echo "<pre>";

$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $perms = fileperms($file);
        $perm_string = substr(sprintf('%o', $perms), -4);
        echo "$file - Permissions: $perm_string - " . (is_dir($file) ? "📁 FOLDER" : "📄 FILE") . "\n";
    }
}
echo "</pre>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";
?>