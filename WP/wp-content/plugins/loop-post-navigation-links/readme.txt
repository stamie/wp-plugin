=== Loop Post Navigation Links ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: posts, navigation, links, next, previous, portfolio, previous_post_link, next_post_link, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 5.5
Stable tag: 3.0.2

Template tags (for use in single.php) to create post navigation loop (previous to first post is last post; next/after last post is first post).


== Description ==

This plugin provides two template tags for use in single.php to create a post navigation loop, whereby previous to the first post is the last post, and after the last post is first post. Basically, when you're on the last post and you click to go to the next post, the link takes you to the first post. Likewise, if you're on the first post and click to go to the previous post, the link takes you to the last post.

The function `c2c_next_or_loop_post_link()` is identical to WordPress's `next_post_link()` in every way except when called on the last post in the navigation sequence, in which case it links back to the first post in the navigation sequence.

The function `c2c_previous_or_loop_post_link()` is identical to WordPress's `previous_post_link()` in every way except when called on the first post in the navigation sequence, in which case it links back to the last post in the navigation sequence.

Useful for providing a looping link of posts, such as for a portfolio, or to continually present pertinent posts for visitors to continue reading.

If you are interested in getting the post itself and not just a link to the post, you can use the `c2c_get_next_or_loop_post()` and `c2c_get_previous_or_loop_post()` functions. If you just want the URL to the post, you can use `c2c_get_next_or_loop_post_url()` and `c2c_get_previous_or_loop_post_url()`.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/loop-post-navigation-links/) | [Plugin Directory Page](https://wordpress.org/plugins/loop-post-navigation-links/) | [GitHub](https://github.com/coffee2code/loop-post-navigation-links/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `wp-content/plugins/`).
2. Activate the plugin through the 'Plugins' admin menu in WordPress.
3. Use `c2c_next_or_loop_post_link()` template tag instead of `next_post_link()`, and/or `c2c_previous_or_loop_post_link()` template tag instead of `previous_post_link()`, in your single-post template (e.g. single.php).


== Template Tags ==

The plugin provides four template tags for use in your single-post theme templates.

= Functions =

* `function c2c_next_or_loop_post_link( $format='%link &raquo;', $link='%title', $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like WordPress's `next_post_link()`, this function displays a link to the next chronological post (among all published posts, those in the same category, or those not in certain categories). Unlink `next_post_link()`, when on the last post in the sequence this function will link back to the first post in the sequence, creating a circular loop.

* `function c2c_get_next_or_loop_post_link( $format='%link &raquo;', $link='%title', $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like `c2c_next_or_loop_post_link(), but returns the value without echoing it.

* `function c2c_previous_or_loop_post_link( $format='&laquo; %link', $link='%title', $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like WordPress's `previous_post_link()`, this function displays a link to the previous chronological post (among all published posts, those in the same category, or those not in certain categories). Unlink `previous_post_link()`, when on the first post in the sequence this function will link to the last post in the sequence, creating a circular loop.

* `function c2c_get_previous_or_loop_post_link( $format='&laquo; %link', $link='%title', $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like `c2c_get_previous_or_loop_post_link(), but returns the value without echoing it.

* `function c2c_get_next_or_loop_post( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like WordPress's `get_adjacent_post()` when used to find the next post, except when on the last post in the sequence this function will return the first post in the sequence, creating a circular loop.

* `function c2c_get_previous_or_loop_post( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Like WordPress's `get_adjacent_post()` when used to find the previous post, except when on the first post in the sequence this function will return the last post in the sequence, creating a circular loop.

* `function c2c_get_next_or_loop_post_url( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Returns the URL for the next post or the post at the beginning of the series.

* `function c2c_get_previous_or_loop_post_url( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' )`
Returns the URL for the previous post or the post at the end of the series.

= Arguments =

* `$format`
(optional) A percent-substitution string indicating the format of the entire output string. Use <code>%link</code> to represent the next/previous post being linked, or <code>%title</code> to represent the title of the next/previous post.

* `$link`
(optional) A percent-substitution string indicating the format of the link itself that gets created for the next/previous post. Use <code>%link</code> to represent the next/previous post being linked, or <code>%title</code> to represent the title of the next/previous post.

* `$in_same_term`
(optional) A boolean value (either true or false) indicating if the next/previous post should be in the current post's same taxonomy term.

* `$excluded_terms`
(optional) An array or comma-separated string of category or term IDs to which posts cannot belong.

* `$taxonomy`
(optional) Taxonomy, if $in_same_term is true. Default 'category'.

== Examples ==

`<div class="loop-navigation">
	<div class="alignleft"><?php c2c_previous_or_loop_post_link(); ?></div>
	<div class="alignright"><?php c2c_next_or_loop_post_link(); ?></div>
</div>`


== Hooks ==

The plugin is further customizable via eleven hooks. Typically, code making use of hooks should ideally be put into a mu-plugin, a site-specific plugin (which is beyond the scope of this readme to explain), or in theme template files. Many of these filters are likely only of interest to advanced users able to code.

**c2c_previous_or_loop_post_link_output, c2c_next_or_loop_post_link_output (filters)**

The 'c2c_previous_or_loop_post_link_output' and 'c2c_next_or_loop_post_link_output' filters allow you to customize the link markup generated for previous and next looping links, respectively.

Arguments:

* $format         (string):       Link anchor format.
* $link           (string):       Link permalink format.
* $in_same_term   (bool):         Optional. Whether link should be in a same taxonomy term. Default is false.
* $excluded_terms (array|string): Optional. Array or comma-separated list of excluded term IDs. Default is ''.
* $previous       (bool):         Optional. Whether to display link to previous or next post. Default is true.
* $taxonomy       (string):       Optional. Taxonomy, if $in_same_term is true. Default 'category'.
* $adjacent       (string):       Whether the post is previous or next.

Example:

  `<?php
    // Prepend "Prev:" to previous link markup.
    function my_custom_previous_or_loop_link_output( $output, $format, $link, $post, $in_same_term, $excluded_terms, $taxonomy ) {
      return 'Prev: ' . $output;
    }
    add_filter( 'c2c_previous_or_loop_post_link_output', 'my_custom_previous_or_loop_link_output', 10, 4 );
  ?>`

**c2c_previous_or_loop_post_link_get, c2c_next_or_loop_post_link_get (filters)**

The 'c2c_previous_or_loop_post_link_get' and 'c2c_next_or_loop_post_link_get' filters allow you to customize the link markups generated for previous and next looping links, respectively, but in the non-echoing functions.

Arguments:

* $output         (string):       The adjacent post link.
* $format         (string):       Link anchor format.
* $link           (string):       Link permalink format.
* $post           (WP_Post):      The adjacent post.
* $in_same_term   (bool):         Optional. Whether link should be in a same taxonomy term. Default is false.
* $excluded_terms (array|string): Optional. Array or comma-separated list of excluded term IDs. Default is ''.
* $previous       (bool):         Optional. Whether to display link to previous or next post. Default is true.
* $taxonomy       (string):       Optional. Taxonomy, if $in_same_term is true. Default 'category'.
* $adjacent       (string):       Whether the post is previous or next.

**c2c_previous_or_loop_post_link, c2c_next_or_loop_post_link, c2c_get_previous_or_loop_post_link, c2c_get_next_or_loop_post_link, c2c_get_adjacent_or_loop_post, c2c_get_previous_or_loop_post, c2c_get_previous_or_loop_post (actions)**

The 'c2c_previous_or_loop_post_link' and 'c2c_next_or_loop_post_link' actions allow you to use an alternative approach to safely invoke `c2c_previous_or_loop_post_link()` and `c2c_next_or_loop_post_link()`, respectively, in such a way that if the plugin were deactivated or deleted, then your calls to the functions won't cause errors in your site. The 'c2c_get_previous_or_loop_post_link' and 'c2c_get_next_or_loop_post_link' filters do the same for the non-echoing `c2c_previous_or_loop_post_link()` and `c2c_next_or_loop_post_link()`.

Arguments:

* Same as for for `c2c_previous_or_loop_post_link()` and `c2c_next_or_loop_post_link()`

Example:

Instead of:

`<?php c2c_previous_or_loop_post_link( '<span class="prev-or-loop-link">&laquo; %link</span>' ); ?>`

Do:

`<?php do_action( 'c2c_previous_or_loop_post_link', '<span class="prev-or-loop-link">&laquo; %link</span>' ); ?>`


== Changelog ==

= 3.0.2 (2020-08-26) =
Highlights:

This minor update has some minor tweaks that should go unnoticed, restructures unit test file structure, adds a TODO.md file, and notes compatibility through WP 5.5+.

Details:

* Change: Unset loop flag in class just after it's used to ensure it gets reset
* Change: Escape URL for post before being output in link (hardening)
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Note compatibility through WP 5.5+
* Unit tests:
    * New: Add test for hook registrations
    * New: Add tests for `modify_nextprevious_post_where()`

= 3.0.1 (2020-05-10) =
* Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS

= 3.0 (2020-01-28) =
Highlights:

* Significant update after a long hiatus! Adds functions for getting the URL of the previous/next post, modernizes unit tests, adds CHANGELOG.md, adds plugin to GitHub, changes plugin initialization, updates compatibility to be for WP 4.9-5.3+, and many other documentation and behind-the-scenes changes.
* Note that there is an incompatible fix for missing and incorrect arguments for the `c2c_{$adjacent}_or_loop_post_link_output` filter. This change won't affect you unless you have custom code making use of the filter, which is unlikely for almost all users.

Details:

* New: Add `c2c_get_next_or_loop_post_url()` and `c2c_get_previous_or_loop_post_url()` to get URL of adjacent/looped post
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* New: Add README.md file
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add more items to the list)
* New: Add inline documentation for hooks
* New: Add GitHub link to readme.txt
* Fix: Add missing argument `$previous` and remove argument `$post` from `c2c_{$adjacent}_or_loop_post_link_output` hook invocation
* Change: Add `$adjacent` argument to a number of filters, to sync with WP core
    * Adds to `{$adjacent}_post_link`, `{$adjacent}_or_loop_post_link`, `c2c_{$adjacent}_or_loop_post_link_get`, and `c2c_{$adjacent}_or_loop_post_link_output`
* Change: Use `c2c_get_adjacent_or_loop_post()` to obtain post, rather than duplicating its functionality in `c2c_get_adjacent_or_loop_post_link()`
* Change: Remove unnecessary (and incorrect) determination of adjacent post in `c2c_adjacent_or_loop_post_link()`
* Change: Use a different variable name to avoid changing variable sent as function argument and later passed as argument to filter
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Use `apply_filters_deprecated()` to formally deprecate the `{$adjacent}_or_loop_post_link` filter
* Change: Update code formatting to match modern WordPress standards
* Change: Remove `load_textdomain()` and just load the textdomain within `init()`
* Unit tests:
    * Change: Change `expected()` to optionally not include arrow quotes
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Enable more error output for unit tests
    * Change: Comment out unit tests that weren't actually testing anything
    * Fix: Don't declare `$posts` as being static since it's never referenced as if it was
    * New: Add tests for hooks
* Change: Note compatibility through WP 5.3+
* Change: Drop compatibility with versions of WP older than 4.9
* Change: Include documentation on filter arguments
* Change: Tweak formatting for installation instructions
* Change: Remove documentation for non-existent parameters of `modify_nextprevious_post_where()`
* Change: Remove unnecessary `echo` in code example
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Rename readme.txt section from 'Filters' to 'Hooks' and provide a better section intro
* Change: Update License URI to be HTTPS
* Change: Update copyright date (2020)
* Change: Update installation instruction to prefer built-in installer over .zip file

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/loop-post-navigation-links/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 3.0.2 =
Trivial update:minor tweaks that should go unnoticed, restructured unit test file structure, added a TODO.md file, and noted compatibility through WP 5.5+.

= 3.0.1 =
Trivial update: Updated a few URLs to be HTTPS and noted compatibility through WP 5.4+.

= 3.0 =
Revival update: added functions for getting URL of previous/next post, modernized unit tests, added CHANGELOG.md, changed plugin initialization, updated compatibility to be WP 4.9-5.3+ updated copyright date (2020), and much more. See changelog for potential breaking change to a filter.

= 2.6.1 =
Trivial update: improved support for localization, minor unit test tweaks, verified compatibility through WP 4.4+, and updated copyright date (2016)

= 2.6 =
Recommended minor update: Added new template tags for getting the adjacent or looped post object; minor bug fixes; noted compatibility through WP 4.3+

= 2.5.2 =
Trivial update: noted compatibility through WP 4.1+ and updated copyright date (2015)

= 2.5.1 =
Trivial update: noted compatibility through WP 4.0+; added plugin icon.

= 2.5 =
Major update: added support for navigating by taxonomy; added non-echoing versions of functions, and more filters; added unit tests; noted compatibility through WP 3.8+; dropped compatibility with WP older than 3.6

= 2.0 =
Recommended major update: synced with changes made to WP; added filters; changed arguments to existing filters; renamed and deprecated all existing functions and filters; noted compatibility through WP 3.5+; and more. (All your old usage will still work, though)

= 1.6.3 =
Trivial update: noted compatibility through WP 3.4+; explicitly stated license

= 1.6.2 =
Trivial update: noted compatibility through WP 3.3+ and updated copyright date

= 1.6.1 =
Trivial update: noted compatibility through WP 3.2+ and updated copyright date

= 1.6 =
Minor update. Highlights: adds 'rel=' attribute to links; minor tweaks; verified WP 3.0 compatibility.
