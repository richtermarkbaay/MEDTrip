<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$src = realpath(__DIR__.'/src');
$build_dir = realpath(__DIR__.'/doc');

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($src)
;

return new Sami($iterator, array(
    'theme'                => 'enhanced',
    'title'                => 'HealthCareAbroad API',
    'build_dir'            => $build_dir,
    'default_opened_level' => 2,
));
