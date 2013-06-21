<?php
/*
Plugin Name: StatsFC Next Fixture
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Next Fixture
Version: 1.0
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
		$defaults = array(
			'title'			=> __('Next Fixture', STATSFC_NEXTFIXTURE_ID),
			'api_key'		=> __('', STATSFC_NEXTFIXTURE_ID),
			'team'			=> __('', STATSFC_NEXTFIXTURE_ID),
			'default_css'	=> __('', STATSFC_NEXTFIXTURE_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$team			= strip_tags($instance['team']);
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
				<?php _e('API key', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_NEXTFIXTURE_ID); ?>:
				<?php
				$data = file_get_contents('http://api.statsfc.com/premier-league/teams.json?key=' . (! empty($api_key) ? $api_key : 'free'));

				try {
					if (empty($data)) {
						throw new Exception('There was an error connecting to the StatsFC API');
					}

					$json = json_decode($data);
					if (isset($json->error)) {
						throw new Exception($json->error);
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('team'); ?>">
						<option></option>
						<?php
						foreach ($json as $row) {
							echo '<option value="' . esc_attr($row->path) . '"' . ($row->path == $team ? ' selected' : '') . '>' . esc_attr($row->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>">
				<?php
				}
				?>
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
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['team']			= strip_tags($new_instance['team']);
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
		$api_key		= $instance['api_key'];
		$team			= $instance['team'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			if (empty($team)) {
				throw new Exception('Please choose a team from the widget options');
			}

			$data = file_get_contents('https://api.statsfc.com/' . esc_attr($team) . '/fixtures.json?key=' . $api_key . '&limit=1');

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);
			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			if (count($json) == 0) {
				throw new Exception('No fixtures found');
			}

			$fixture = current($json);

			if ($default_css) {
				wp_register_style(STATSFC_NEXTFIXTURE_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_NEXTFIXTURE_ID . '-css');
			}

			$home			= esc_attr($fixture->home);
			$away			= esc_attr($fixture->away);
			$homePath		= esc_attr(str_replace(' ', '-', strtolower($fixture->home)));
			$homeClass		= esc_attr(str_replace(' ', '', strtolower($fixture->home)));
			$awayPath		= esc_attr(str_replace(' ', '-', strtolower($fixture->away)));
			$awayClass		= esc_attr(str_replace(' ', '', strtolower($fixture->away)));
			$competition	= esc_attr($fixture->competition);
			$date			= self::_convertDate($fixture->date, 'l, jS F Y');
			$time			= self::_convertDate($fixture->date, 'H:i');
			?>
			<div class="statsfc_nextfixture">
				<table>
					<tbody>
						<tr>
							<td class="statsfc_home statsfc_badge_<?php echo $homeClass; ?>">
								<img src="//cdn.statsfc.com/<?php echo $homePath; ?>.png" title="<?php echo $home; ?>" width="80" height="80"><br>
								<span class="statsfc_team"><?php echo $home; ?></span>
							</td>
							<td class="statsfc_details">
								<span class="statsfc_competition"><?php echo $competition; ?></span><br>
								<span class="statsfc_date"><?php echo $date; ?></span><br>
								<span class="statsfc_time"><?php echo $time; ?></span>
							</td>
							<td class="statsfc_away statsfc_badge_<?php echo $awayClass; ?>">
								<img src="//cdn.statsfc.com/<?php echo $awayPath; ?>.png" title="<?php echo $away; ?>" width="80" height="80"><br>
								<span class="statsfc_team"><?php echo $away; ?></span>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
			</div>
		<?php
		} catch (Exception $e) {
			echo '<p class="statsfc_error">' . esc_attr($e->getMessage()) .'</p>' . PHP_EOL;
		}

		echo $after_widget;
	}

	private static function _convertDate($timestamp, $format) {
		$datetime = new DateTime($timestamp, new DateTimeZone('GMT'));
		$datetime->setTimezone(new DateTimeZone(get_option('timezone_string')));

		return $datetime->format($format);
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_NEXTFIXTURE_ID . '");'));
?>