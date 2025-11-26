<?php
$src = 'C:/Users/online sale/Downloads/backup_2025-11-23_07-36-47.sql';
$dst = __DIR__ . '/public/backup_clean2.sql';
if (!file_exists($src)) {
    echo "Source file not found: $src\n";
    exit(1);
}
$fh = fopen($src, 'r');
$out = fopen($dst, 'w');
if (!$fh || !$out) {
    echo "Failed to open files\n";
    exit(1);
}
while (($line = fgets($fh)) !== false) {
    if (strpos($line, 'mysqldump:') === 0) continue;
    // also skip lines that are just warnings from mysqldump
    if (preg_match('/^Warning:|^-- Dump completed on/', $line)) continue;
    fwrite($out, $line);
}
fclose($fh);
fclose($out);
echo "Cleaned file written to: $dst\n";
