#!/bin/bash
# Local Travis :-)
find . \( -name '*.php' \) -exec php -lf {} \;
jshint .
jscs .
phpcs -p -s -v -n . --standard=./codesniffer.ruleset.xml --extensions=php -a