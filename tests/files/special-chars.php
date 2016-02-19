<div>
    <p><?php __ ( 'plain' ); ?></p>
    <p><?php __('DATE \a\t TIME'); ?></p>
    <p><?php __("DATE \a\\t TIME"); ?></p>
    <p><?php __("DATE \\a\\t TIME"); ?></p>
    <p><?php __("FIELD\tFIELD"); ?></p>
    <p><?php __(
        "text "
        // test
        .'concatenated'.
        /* test*/ " with 'comments'"
    ); ?></p>
    <p><?php __($avoid['me']); ?>
    <p><?php __('Stop at the variable'.$var.'!'); ?>
</div>
