<?php
$f = 'resources/views/marketing/templates.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/<main>.*?<\/main>/s', '<main>%%MAIN_CONTENT%%</main>', $c);
file_put_contents($f, $c);
