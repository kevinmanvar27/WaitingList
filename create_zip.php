<?php
// Name of the output zip file
$zipFile = 'backup_' . date('Y-m-d_H-i-s') . '.zip';

// Create new ZipArchive instance
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    exit("❌ Cannot open <$zipFile>\n");
}

// Create recursive directory iterator
$rootPath = realpath('.');
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

// Add files to zip
foreach ($files as $name => $file) {
    // Skip the ZIP file itself
    if ($file->getRealPath() === realpath($zipFile)) {
        continue;
    }

    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($rootPath) + 1);

    if ($file->isFile()) {
        $zip->addFile($filePath, $relativePath);
    } elseif ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    }
}

// Close zip
$zip->close();

echo "✅ ZIP file created successfully: $zipFile";
?>
