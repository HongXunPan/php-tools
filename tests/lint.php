<?php

$roots = [
    dirname(__DIR__) . '/src',
    __DIR__,
];

$failed = [];
foreach ($roots as $root) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($files as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }

        $output = [];
        $exitCode = 0;
        exec(
            escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()),
            $output,
            $exitCode
        );
        if ($exitCode !== 0) {
            $failed[] = $file->getPathname() . PHP_EOL . implode(PHP_EOL, $output);
        }
    }
}

if ($failed) {
    fwrite(STDERR, implode(PHP_EOL, $failed) . PHP_EOL);
    exit(1);
}

echo '全部 PHP 文件语法检查通过。' . PHP_EOL;
