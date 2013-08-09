=== StatsFC Next Fixture ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, fixtures, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will show the next fixture for a Premier League team on your website.

== Description ==

Add the next fixture for any Premier League team to your WordPress website. To request an API key sign up for free at [statsfc.com](https://statsfc.com).

Fixture times are automatically adjusted to your website's local time.

For a demo, check out [wp.statsfc.com](http://wp.statsfc.com).

== Installation ==

1. Upload the `statsfc-next-fixture` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the API key and any other options. If you don't have any API key, sign up for free at statsfc.com

If you want to place the widget into a page rather than a sidebar:

1. Install and activate 'Widgets on Pages' from the 'Plugins' menu in WordPress
2. Add a sidebar named "StatsFC Next Fixture" from the 'Settings > Widgets on Pages' menu in WordPress
3. Place the widget anywhere in a page, using the following code:

		[widgets_on_pages id="StatsFC Next Fixture"]

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**:

- Fixed timezone adjustment bug in old versions of PHP.
- If using an old version, you'll need to choose your own UTC offset in the options.

**1.0.2**:

- Use cURL to fetch API data if possible.

**1.0.3**:

- Use short team names.

**1.0.4**:

- Minor team shirt CSS bug fixes.

**1.0.5**:

- Fixed possible cURL bug.

**1.0.6**:

- Added fopen fallback if cURL request fails.

== Upgrade notice ==

