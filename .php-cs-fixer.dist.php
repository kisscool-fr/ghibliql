<?php

$finder = PhpCsFixer\Finder::create()    
    ->in(__DIR__ . '/lib')
    ->in(__DIR__ . '/public')
    ->in(__DIR__ . '/tests')
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;