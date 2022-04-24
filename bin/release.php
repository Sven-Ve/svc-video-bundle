#!/usr/bin/env php
<?php

$version = "1.10.1";
$message = "ready for symfony 5.4 and 6 and newer Svc bundles";

file_put_contents("CHANGELOG.md", "\n\n## Version " . $version, FILE_APPEND);
file_put_contents("CHANGELOG.md", "\n*" . date("r") . "*", FILE_APPEND);
file_put_contents("CHANGELOG.md", "\n- " . $message . "\n", FILE_APPEND);

$res = shell_exec('git add .');
$res = shell_exec('git commit -S -m "' . $message . '"');
$res = shell_exec('git push');

$res = shell_exec('git tag -a -s ' . $version . ' -m "' . $message . '"');
$res = shell_exec('git push origin ' . $version);

?>
