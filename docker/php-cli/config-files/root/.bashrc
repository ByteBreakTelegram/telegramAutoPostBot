#!/bin/sh
cat <<'MSG'
Laravel short command 2
MSG

echo "PHP version: ${PHP_VERSION}"
echo "Composer version: $(php -r "preg_match('~([0-9]\.[0-9]+\.?[0-9]*)~', '$(composer --version)', \$matches); echo \$matches[0];")"
echo "ICU version: $(icu-config --version)"
if [ $(php -m -c | grep xdebug) ]; then
    echo "XDebug version: $(php -r 'echo phpversion("xdebug");')"
else
    echo "XDebug disabled"
fi
if [ $(php -m -c | grep xhprof) ]; then
    echo "xhprof version: $(php -r 'echo phpversion("xhprof");')"
else
    echo "xhprof disabled"
fi
if [ $(php -m -c | grep elastic_apm) ]; then
    echo "elastic_apm version: $(php -r 'echo phpversion("elastic_apm");')"
else
    echo "elastic_apm disabled"
fi
