function _(s) {
	return typeof l10n[s] != 'undefined' ? l10n[s] : s;
}
function test(param) {
	var a = __("Hello world, testing jsgettext");
	func(__('Test string'));
	var reg1 = /"[a-z]+"/i;
	var reg2 = /[a-z]+\+\/"aa"/i;
	var s1 = __('string 1: single quotes');
	var s2 = __("string 2: double quotes");
	var s3 = __("/* comment in string */");
	var s4 = __("regexp in string: /[a-z]+/i");
	var s5 = jsgettext( "another function" );
	var s6 = avoidme("should not see me!");
	var s7 = T_("string 2: \"escaped double quotes\"");
	var s8 = __('string 2: \'escaped single quotes\'');
	var s9 = T_('¡¡¿¿Texto con açentos, eñes, etcétera??!!');

	// "string in comment"
	//;

	/**
	 * multiple
	 * lines
	 * comment
	 * _("Hello world from comment")
	 */
}
