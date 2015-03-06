<?php
/*
Plugin Name: StatsFC Next Fixture
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Next Fixture
Version: 1.6.2
Author: Will Woodward
Author URI: http://willjw.co.uk
License: GPL2
*/

/*  Copyright 2013  Will Woodward  (email : will@willjw.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('STATSFC_NEXTFIXTURE_ID',	'StatsFC_NextFixture');
define('STATSFC_NEXTFIXTURE_NAME',	'StatsFC Next Fixture');

/**
 * Adds StatsFC widget.
 */
class StatsFC_NextFixture extends WP_Widget {
	public $isShortcode = false;

	private static $defaults = array(
		'title'			=> '',
		'key'			=> '',
		'team'			=> '',
		'competition'	=> '',
		'date'			=> '',
		'timezone'		=> 'Europe/London',
		'default_css'	=> true
	);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(STATSFC_NEXTFIXTURE_ID, STATSFC_NEXTFIXTURE_NAME, array('description' => 'StatsFC Next Fixture'));
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$instance		= wp_parse_args((array) $instance, self::$defaults);
		$title			= strip_tags($instance['title']);
		$key			= strip_tags($instance['key']);
		$team			= strip_tags($instance['team']);
		$competition	= strip_tags($instance['competition']);
		$date			= strip_tags($instance['date']);
		$timezone		= strip_tags($instance['timezone']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('Title', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Key', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Competition', STATSFC_NEXTFIXTURE_ID); ?>:
				<?php
				try {
					$data = $this->_fetchData('https://api.statsfc.com/crowdscores/competitions.php');

					if (empty($data)) {
						throw new Exception;
					}

					$json = json_decode($data);

					if (isset($json->error)) {
						throw new Exception;
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('competition'); ?>">
						<option value="">Any</option>
						<?php
						foreach ($json as $comp) {
							echo '<option value="' . esc_attr($comp->key) . '"' . ($comp->key == $competition ? ' selected' : '') . '>' . esc_attr($comp->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('competition'); ?>" type="text" value="<?php echo esc_attr($competition); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Date (YYYY-MM-DD)', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo esc_attr($date); ?>" placeholder="YYYY-MM-DD">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Timezone', STATSFC_NEXTFIXTURE_ID); ?>:
				<select class="widefat" name="<?php echo $this->get_field_name('timezone'); ?>">
					<?php
					$zones = timezone_identifiers_list();

					foreach ($zones as $zone) {
						echo '<option value="' . esc_attr($zone) . '"' . ($zone == $timezone ? ' selected' : '') . '>' . esc_attr($zone) . '</option>' . PHP_EOL;
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', STATSFC_NEXTFIXTURE_ID); ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
			</label>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['key']			= strip_tags($new_instance['key']);
		$instance['team']			= strip_tags($new_instance['team']);
		$instance['competition']	= strip_tags($new_instance['competition']);
		$instance['date']			= strip_tags($new_instance['date']);
		$instance['timezone']		= strip_tags($new_instance['timezone']);
		$instance['default_css']	= strip_tags($new_instance['default_css']);

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
		extract($args);

		$title			= apply_filters('widget_title', $instance['title']);
		$key			= $instance['key'];
		$team			= $instance['team'];
		$competition	= $instance['competition'];
		$date			= $instance['date'];
		$timezone		= $instance['timezone'];
		$default_css	= filter_var($instance['default_css'], FILTER_VALIDATE_BOOLEAN);

		$html  = $before_widget;
		$html .= $before_title . $title . $after_title;

		try {
			if (strlen($team) == 0) {
				throw new Exception('Please choose a team from the widget options');
			}

			$data = $this->_fetchData('https://api.statsfc.com/crowdscores/next-fixture.php?key=' . urlencode($key) . '&team=' . urlencode($team) . '&competition=' . urlencode($competition) . '&date=' . urlencode($date) . '&timezone=' . urlencode($timezone));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			$match		= $json->match;
			$customer	= $json->customer;

			if ($default_css) {
				wp_register_style(STATSFC_NEXTFIXTURE_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_NEXTFIXTURE_ID . '-css');
			}

			$homeBadge		= esc_attr($match->homepath);
			$awayBadge		= esc_attr($match->awaypath);
			$home			= esc_attr($match->home);
			$away			= esc_attr($match->away);
			$competition	= esc_attr($match->competition);
			$details		= '';

			if (! $match->started) {
				$date	= esc_attr($match->date);
				$time	= esc_attr($match->time);

				$details = <<< HTML
				<span class="statsfc_date">{$date}</span><br>
				<span class="statsfc_time">{$time}</span>
HTML;
			} else {
				$status		= esc_attr($match->status);
				$homeScore	= esc_attr($match->score[0]);
				$awayScore	= esc_attr($match->score[1]);

				$details = <<< HTML
				<span>
					<small>Live: {$status}</small><br>
					{$homeScore} - {$awayScore}
				</span>
HTML;
			}

			$html .= <<< HTML
			<div class="statsfc_nextfixture">
				<table>
					<tbody>
						<tr>
							<td class="statsfc_home statsfc_badge_{$homeBadge}">
								<img src="//api.statsfc.com/kit/{$homeBadge}.svg" title="{$home}" width="80" height="80"><br>
								<span class="statsfc_team">{$home}</span>
							</td>
							<td class="statsfc_details">
								<span class="statsfc_competition">{$competition}</span><br>
								<span>{$details}</span>
							</td>
							<td class="statsfc_away statsfc_badge_{$awayBadge}">
								<img src="//api.statsfc.com/kit/{$awayBadge}.svg" title="{$away}" width="80" height="80"><br>
								<span class="statsfc_team">{$away}</span>
							</td>
						</tr>
					</tbody>
				</table>
HTML;

			if ($customer->adSupported) {
				wp_register_script(STATSFC_NEXTFIXTURE_ID . '-ad-js', plugins_url('ad.js', __FILE__), array('jquery'));
				wp_enqueue_script(STATSFC_NEXTFIXTURE_ID . '-ad-js');

				$html .= <<< HTML
				<div class="statsfc_ad"></div>
HTML;
			}

			if ($customer->attribution) {
				$html .= <<< HTML
				<p class="statsfc_footer"><small>Powered by StatsFC.com. Fan data via CrowdScores.com</small></p>
HTML;
			}

			$html .= <<< HTML
			</div>
HTML;
		} catch (Exception $e) {
			$html .= '<p style="text-align: center;">StatsFC.com – ' . esc_attr($e->getMessage()) . '</p>' . PHP_EOL;
		}

		$html .= $after_widget;

		if ($this->isShortcode) {
			return $html;
		} else {
			echo $html;
		}
	}

	private function _fetchData($url) {
		$response = wp_remote_get($url);

		return wp_remote_retrieve_body($response);
	}

	public static function shortcode($atts) {
		$args = shortcode_atts(self::$defaults, $atts);

		$widget					= new self;
		$widget->isShortcode	= true;

		return $widget->widget(array(), $args);
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_NEXTFIXTURE_ID . '");'));
add_shortcode('statsfc-next-fixture', STATSFC_NEXTFIXTURE_ID . '::shortcode');
