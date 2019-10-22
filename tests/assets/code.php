<header>
    <h1><?php __('text 1'); ?></h1>
</header>

<div>
    <p><?= __('$var'); ?></p>
    <p><?= p__('context', 'text 1 with context'); ?></p>
    <p><?= noop__('text 2'); ?></p>
    <p><?= __('text 3 (with parenthesis)'); ?></p>
    <p><?= __('text 4 "with double quotes"'); ?></p>
    <p><?= __('text 5 \'with escaped single quotes\''); ?></p>
</div>

<div>
    <p><?= __('text 6'); ?></p>
    <p><?= __('text 7 (with parenthesis)'); ?></p>
    <p><?= __('text 8 "with escaped double quotes"'); ?></p>
    <p><?= __("text 9 'with single quotes'"); ?></p>
    <p><?php echo n__('text 10 with plural', 'The plural form', 5); ?></p>
</div>

<?php echo __("<div id=\"blog\" class=\"container\">
    <div class=\"row\">
        <div class=\"col-md-12\">
            <div id=\"content\">
                <div class=\"page_post\">
                    <div class=\"container\">
                        <div class=\"margin-top-40\"></div>
                        <div class=\"col-sm-3 col-md-2 centered-xs an-number\">4</div>
                    </div>
                </div>
                <div class=\"container\">
                    <h1 class=\"text-center margin-top-10\">Sorry, but we couldn't find this page</h1>
                    <div id=\"body-div\">
                        <div id=\"main-div\">
                            <div class=\"text-404\">
                                <div>
                                    <p>Maybe you have entered an incorrect URL of the page or page moved to another section or just page is temporarily unavailable.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>");
?>
<p><?php __(''); ?></p>
<div>
    <p><?php __ ( 'plain' ); ?></p>
    <p><?php gettext('DATE \a\t TIME'); ?></p>
    <p><?php __("DATE \a\\t TIME"); ?></p>
    <p><?php __("DATE \\a\\t TIME"); ?></p>
    <p><?php __("FIELD\tFIELD"); ?></p>
    <p><?php __(
        "text "
        // test
        .'concatenated'.
        /* test*/ " with 'comments'"
    ); ?></p>

    <p><?php __('Stop at the variable'.$var.'!'); ?>
</div>

<?php
__('No comments');
/* All comments */
p__(CONTEXT, 'All comments');
/* Invalid i18n Tagged comment */
__('i18n Tagged comment');
gettext(
    /* i18n Tagged comment inside */
    'i18n Tagged comment inside'
);
dn__('null', 'One comment', 'Many comments', 2);
/* i18n Tagged comment on the line before */
sprintf( __('i18n tagged %s'), '$var');
/*
 * Translators: This is a
 * multi-line comment.
 */
__( 'foo' );
/* translators: this should get extracted. */ $foo = __( 'bar' );
function foo() {
	/*
	 * translators: this comment is
	 * indented with a tab.
	 */
	__( 'foo bar' );
}


__(
/*allowed1 Comment 1 */
/*allowed2 Comment 2 */
/* Comment 4 */
/*not-allowed Comment 3 */
	'Translation with comments'
);
/* allowed1: boo */ /* allowed2: only this should get extracted. */ /* some other comment */ $bar = strtolower( __( 'Foo' ) );

dgettext('domain1', 'matching 1');
dngettext('domain1', 'matching 2 singular', 'matching 2 plural', 1);
dnp__('domain1', 'context', 'matching 3 context singular', 'matching 3 context plural', 123);
d__('domain1', 'matching 4');
dngettext('domain2', 'skip singular', 'skip plural', 2);
dgettext('domain2', 'skip');
__('skip global 1');
gettext('skip global 2');

__('no domain');
dgettext('domain1', 'domain1 Text');
dnp__('domain1', 'context', 'domain1 Text', 'domain1 Text plural', 123);
dngettext('domain2', 'domain2 Text', 'domain2 Text plural', 2);
dgettext('domain2', 'domain2 Text Separate');
d__('domain3', 'domain3 Text');
d__('domain4', 'domain4 Text not scanned');
gettext('no domain 2');

