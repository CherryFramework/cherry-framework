[![license](https://img.shields.io/badge/license-GPL--v.3.0%2B-blue.svg?style=flat-square)](https://github.com/CherryFramework/cherry-framework/blob/master/LICENSE)
[![Build Status](https://travis-ci.org/CherryFramework/cherry-framework.svg?branch=master)](https://travis-ci.org/CherryFramework/cherry-framework)

# cherry-framework
Module system.

## Authors

* [@MaksimTS](https://github.com/MaksimTS) - **Manager**
* [@Cheh](https://github.com/cheh) - **Developer**
* [@Behaart](https://github.com/MakhonkoDenis) - **Developer**
* [@MjHead](https://github.com/MjHead) - **Developer**
* [@Sargas](https://github.com/SargasTM) - **Developer**
* [@Shin](https://github.com/shinTM) - **Developer**

## Changelog

### v1.4.3

* ADD: new module - `cherry5-assets-loader`
* ADD: lock-option feature
* ADD: 3rd parameter `$this` for `cherry_breadcrumbs_items` filter
* ADD: new property - module version (using in register/enqueue css and js files)
* ADD: `dropdown-pages` control in `cherry-customizer` module
* UPD: style for widgets
* UPD: use `cherry-interface-builder` in widget form
* UPD: remove deprecated methods in `cherry-widget-factory` module
* UPD: added check for AJAX-handlers in `cherry-handler` module
* UPD: copyright years
* FIX: `cherry-handler` module
* FIX: placeholder attribute in `UI-select`
* FIX: allow home link in breadcrumbs inherit main page title
* FIX: function `get_terms_array()` in `cherry-utility` module
* FIX: HTML validation for `UI-elements`

### v1.4.2

* HOTFIX: `iconpicker` control for `cherry-customizer` module

### v1.4.1

* ADD: allow to filter CSS reserved words while parsing functions
* ADD: async query in `cherry-handler` module
* UPD: UI-kit styles (UI-button, UI-text, UI-textarea, UI-stepper, UI-colorpicker, UI-switcher, UI-select, UI-media)
* UPD: use `wp_add_inline_style` instead of `wp_head` for printing inline CSS
* UPD: UI-button class prefix
* UPD: `cherry5-insert-shortcode` module styles
* UPD: allow to use description argument in iconpicker
* FIX: UI-repeater, UI-radio master and slave bug
* FIX: `cherry-template-manager` module
* FIX: change `meta_key` in `cherry-utility` module - #149

### v1.4.0

* ADD: new modules - `cherry5-insert-shortcode`, `cherry-db-udpates`
* ADD: text-domain
* FIX: compatibility with WordPress 4.7
* FIX: sanitization method in `cherry-utility` - #141
* FIX: duplicate argument in UI-button - #126
* UPD: license link in php-file headers

### v1.3.1

* ADD: macros filter into `cherry-template-manager` module
* ADD: function `bg-image()` into `cherry-dynamic-css` module
* UPD: `cherry-interface-builder` module styles
* UPD: Google fonts json-file
* FIX: Issues [#124](https://github.com/CherryFramework/cherry-framework/issues/124)
* FIX: Issues [#123](https://github.com/CherryFramework/cherry-framework/issues/123)
* FIX: Issues [#120](https://github.com/CherryFramework/cherry-framework/issues/120)
* FIX: Issues [#118](https://github.com/CherryFramework/cherry-framework/issues/118)
* FIX: Issues [#116](https://github.com/CherryFramework/cherry-framework/issues/116)
* FIX: Issues [#115](https://github.com/CherryFramework/cherry-framework/issues/115)
* FIX: UI-media button
* DEL: system notices in `cherry-handler` module

### v1.3.0

* ADD: UI-button
* ADD: new modules - `cherry-handler`, `cherry-template-manager`
* ADD: dynamic CSS collector
* UPD: re-factoring methods calling in `cherry-post-meta` module
* FIX: replace `file_get_contents` to prevent validation errors
* FIX: post meta saving
* FIX: [#81](https://github.com/CherryFramework/cherry-framework/issues/81)
* FIX: [#96](https://github.com/CherryFramework/cherry-framework/issues/96)
* FIX: [#100](https://github.com/CherryFramework/cherry-framework/issues/100)
* FIX: [#102](https://github.com/CherryFramework/cherry-framework/issues/102)
* FIX: [#109](https://github.com/CherryFramework/cherry-framework/issues/109)
* DEL: `cherry-page-builder` module

### v1.2.0

* ADD: new module: `cherry-interface-builder`
* UPD: `cherry-utility` module:
    1) fix for the `cut_text` method
    2) added `get_placeholder_url` method
* UPD: UI-elements:
    1) added an option to disable ui_kit for the repeater element
    2) updated master/salve js logic in UI-elements
    3) updated HTML markup for UI-switcher, `input type="hiden"` replaced with double `input type="radio"`
* UPD: `cherry-customizer` module: file system method replaced with native WordPress method
* UPD: `cherry-post-meta` module:
    1) added data processing procedure before saving to the database
    2) added an option to add columns to the post listing page in the admin panel.
* FIX: PHP-errors in `cherry-post-format-api`
* DEL: remove unnecessary modules: `cherry-taxonomies`, `cherry-post-types`, `cherry-creator`

### v1.1.0

* FIX: saving process in `cherry-post-meta` module

### v1.0.0

* Init stable version


## Help
Found a bug? Feature requests? [Create an issue - Thanks!](https://github.com/CherryFramework/cherry-framework/issues/new)

## Docs

1. [Quick Start Guide](http://www.cherryframework.com/quick-start/)
2. [Real example](https://github.com/CherryFramework/cherry-framework-example)
3. [Documentation](http://www.cherryframework.com/docs/)