<?php
echo "<h1>🔍 Debug Information</h1>";
echo "<h2>Current Directory: " . __DIR__ . "</h2>";
echo "<h2>Files in root directory:</h2>";
echo "<pre>";

function listDir($dir, $indent = '') {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $path = $dir . '/' . $file;
            $perms = fileperms($path);
            $perm_string = substr(sprintf('%o', $perms), -4);
            echo $indent . "$file - Permissions: $perm_string - " . (is_dir($path) ? "📁 FOLDER" : "📄 FILE") . "\n";
            
            if (is_dir($path) && $file == 'admin') {
                // Show admin folder contents
                echo $indent . "  📁 ADMIN FOLDER CONTENTS:\n";
                $admin_files = scandir($path);
                foreach ($admin_files as $afile) {
                    if ($afile != "." && $afile != "..") {
                        $aperms = fileperms($path . '/' . $afile);
                        $aperm_string = substr(sprintf('%o', $aperms), -4);
                        echo $indent . "    - $afile (perms: $aperm_string)\n";
                    }
                }
            }
        }
    }
}

listDir(__DIR__);

echo "</pre>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";
?>