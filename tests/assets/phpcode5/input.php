<?php

__('no domain');

dgettext('domain1', 'domain1 Text');
dnp__('domain1', 'context', 'domain1 Text', 'domain1 Text plural', 123);

dngettext('domain2', 'domain2 Text', 'domain2 Text plural', 2);
dgettext('domain2', 'domain2 Text Separate');

d__('domain3', 'domain3 Text');
d__('domain4', 'domain4 Text not scanned');

gettext('no domain 2');