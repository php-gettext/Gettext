<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Twig_Loader_Array;
use Twig_Environment;
use Twig_Source;
use Twig_Extensions_Extension_I18n;

/**
 * Class to get gettext strings from twig files returning arrays.
 */
class Twig extends Extractor implements ExtractorInterface
{
    public static $options = [
        'extractComments' => 'notes:',
        'twig' => null,
        'ast' => [
            'constants' => [],
            'functions' => [
                // WordPress defaults
                '__'              => 'text_domain',
                '_e'              => 'text_domain',
                '_x'              => 'text_context_domain',
                '_ex'             => 'text_context_domain',
                '_n'              => 'single_plural_number_domain',
                '_nx'             => 'single_plural_number_context_domain',
                '_n_noop'         => 'single_plural_domain',
                '_nx_noop'        => 'single_plural_context_domain'
            ]
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $options += static::$options;

        $twig = $options['twig'] ?: self::createTwig();

        PhpCode::fromString($twig->compileSource(new Twig_Source($string, '')), $translations, $options);
    }

    /**
     * Returns a Twig instance.
     *
     * @return Twig_Environment
     */
    private static function createTwig()
    {
        $twig = new Twig_Environment(new Twig_Loader_Array(['' => '']));
        $twig->addExtension(new Twig_Extensions_Extension_I18n());

        return static::$options['twig'] = $twig;
    }

    /**
     * Register intoa Twig instance additional functions recognized by Timber,
     * the Twig for WordPress library.
     *
     * @return NULL
     */
    private static function enableTimber() {
        $timber = new \Timber\Twig();
        $timber->add_timber_functions(static::$options['twig']);
        $timber->add_timber_filters(static::$options['twig']);
    }

}
