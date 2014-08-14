<header>
    <h1><?php __e('text 1'); ?></h1>
</header>

<div>
    <p><?php __($var); ?></p>
    <p><?php p__('context', $var); ?></p>
    <p><?php __('text 2'); ?></p>
    <p><?php __('text 3 (with parenthesis)'); ?></p>
    <p><?php __('text 4 "with double quotes"'); ?></p>
    <p><?php __('text 5 \'with escaped single quotes\''); ?></p>
</div>

<div>
    <p><?php __("text 2"); ?></p>
    <p><?php __("text 3 (with parenthesis)"); ?></p>
    <p><?php __("text 4 \"with escaped double quotes\""); ?></p>
    <p><?php __("text 5 'with single quotes'"); ?></p>
</div>
