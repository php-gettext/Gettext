<header>
    <h1>{{ __('text 1') }}</h1>
</header>

<div>
    <p>{{ __('text 2') }}</p>
    <p>{{ __('text 3 (with parenthesis)') }}</p>
    <p>{{ __('text 4 "with double quotes"') }}</p>
    <p>{{ __('text 5 \'with escaped single quotes\'') }}</p>
</div>

<div>
    <p>{{ __("text 6") }}</p>
    <p>{{ __("text 7 (with parenthesis)") }}</p>
    <p>{{ __("text 8 \"with escaped double quotes\"") }}</p>
    <p>{{ __("text 9 'with single quotes'") }}</p>
    <p>
    {# This is an actual note for translators. #}
    {{ _n("text 10 with plural", "The plural form", 5) }}
    </p>
    <p>
    {{ _nx("test", "tests", 2, "This gives some context.") }}
</div>
