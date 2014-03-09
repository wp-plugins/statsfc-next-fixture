<?php
/*
Plugin Name: StatsFC Next Fixture
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Next Fixture
Version: 1.2.1
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
			'timezone'		=> __('Europe/London', STATSFC_NEXTFIXTURE_ID),
			'default_css'	=> __('', STATSFC_NEXTFIXTURE_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$team			= strip_tags($instance['team']);
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
				<?php _e('API key', STATSFC_NEXTFIXTURE_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
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
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['team']			= strip_tags($new_instance['team']);
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
		$api_key		= $instance['api_key'];
		$team			= $instance['team'];
		$timezone		= $instance['timezone'];
		$default_css	= $instance['default_css'];

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			if (strlen($team) == 0) {
				throw new Exception('Please choose a team from the widget options');
			}

			$data = $this->_fetchData('https://api.statsfc.com/widget/next-fixture.json.php?key=' . urlencode($api_key) . '&team=' . urlencode($team) . '&timezone=' . urlencode($timezone));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);

			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			$fixture	= $json->fixture;
			$customer	= $json->customer;

			if ($default_css) {
				wp_register_style(STATSFC_NEXTFIXTURE_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_NEXTFIXTURE_ID . '-css');
			}

			$home			= esc_attr($fixture->home);
			$away			= esc_attr($fixture->away);
			$homePath		= esc_attr($fixture->homepath);
			$awayPath		= esc_attr($fixture->awaypath);
			$competition	= esc_attr($fixture->competition);
			$date			= esc_attr($fixture->date);
			$time			= esc_attr($fixture->time);
			?>
			<div class="statsfc_nextfixture">
				<table>
					<tbody>
						<tr>
							<td class="statsfc_home">
								<img src="//api.statsfc.com/kit/<?php echo $homePath; ?>.png" title="<?php echo $home; ?>" width="80" height="80"><br>
								<span class="statsfc_team"><?php echo $home; ?></span>
							</td>
							<td class="statsfc_details">
								<span class="statsfc_competition"><?php echo $competition; ?></span><br>
								<span class="statsfc_date"><?php echo $date; ?></span><br>
								<span class="statsfc_time"><?php echo $time; ?></span>
							</td>
							<td class="statsfc_away">
								<img src="//api.statsfc.com/kit/<?php echo $awayPath; ?>.png" title="<?php echo $away; ?>" width="80" height="80"><br>
								<span class="statsfc_team"><?php echo $away; ?></span>
							</td>
						</tr>
					</tbody>
				</table>

				<?php
				if ($customer->advert) {
				?>
					<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
				<?php
				}
				?>
			</div>
		<?php
		} catch (Exception $e) {
		?>
			<p>StatsFC.com – <?php echo esc_attr($e->getMessage()); ?></p>
		<?php
		}

		echo $after_widget;
	}

	private function _fetchData($url) {
		if (function_exists('curl_exec')) {
			return $this->_curlRequest($url);
		} else {
			return $this->_fopenRequest($url);
		}
	}

	private function _curlRequest($url) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 5,
			CURLOPT_URL				=> $url
		));

		$data = curl_exec($ch);
		if (empty($data)) {
			$data = $this->_fopenRequest($url);
		}

		curl_close($ch);

		return $data;
	}

	private function _fopenRequest($url) {
		return file_get_contents($url);
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_NEXTFIXTURE_ID . '");'));