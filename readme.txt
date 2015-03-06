=== StatsFC Next Fixture ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, fixtures, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 4.0
Stable tag: 1.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will show the next fixture for a Premier League team on your website.

== Description ==

Add the next fixture for any Premier League team to your WordPress website. To request a key sign up for your free trial at [statsfc.com](https://statsfc.com).

Fixture times are automatically adjusted to your website's local time.

For a demo, check out [wp.statsfc.com/next-fixture/](http://wp.statsfc.com/next-fixture/).

== Installation ==

1. Upload the `statsfc-next-fixture` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the StatsFC key and any other options. If you don't have a key, sign up for free at [statsfc.com](https://statsfc.com)

You can also use the `[statsfc-next-fixture]` shortcode, with the following options:

- `key` (required): Your StatsFC key
- `team` (required): Team name, e.g., `Liverpool`
- `competition` (optional): Competition key, e.g., `EPL`
- `date` (optional): For a back-dated match, e.g., `2013-12-31`
- `timezone` (optional): The timezone to convert match times to, e.g., `Europe/London` ([complete list](https://php.net/manual/en/timezones.php))
- `default_css` (optional): Use the default widget styles, `true` or `false`

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**: Fixed timezone adjustment bug in old versions of PHP. If using an old version, you'll need to choose your own UTC offset in the options.

**1.0.2**: Use cURL to fetch API data if possible.

**1.0.3**: Use short team names.

**1.0.4**: Minor team shirt CSS bug fixes.

**1.0.5**: Fixed possible cURL bug.

**1.0.6**: Added fopen fallback if cURL request fails.

**1.0.7**: Fixed possible Timezone bug.

**1.1**: Show live score if there's a match ongoing.

**1.1.1**: Tweaked error message.

**1.2**: Allow an actual timezone to be selected, and use the new API.

**1.2.1**: Improved timezone list.

**1.3**: Updated to use the new API.

**1.4**: Added a `date` parameter.

**1.5**: Added `[statsfc-next-fixture]` shortcode.

**1.5.2**: Updated team badges.

**1.5.3**: Default `default_css` parameter to `true`

**1.5.4**: Added badge class for each team

**1.5.5**: Use built-in WordPress HTTP API functions

**1.6**: Enabled ad-support

**1.6.1**: Added a `competition` parameter

**1.6.2**: Allow more discrete ads for ad-supported accounts

== Upgrade notice ==

