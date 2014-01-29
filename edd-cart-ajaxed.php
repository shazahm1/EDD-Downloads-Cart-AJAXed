<?php
/**
 * An Extension for Easy Digital downloads that adds a new widget that
 * loads the downloads cart HTML via an AJAX request.
 *
 * @package   Easy Digital Downloads Cart Widget - AJAXed
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      http://connections-pro.com
 * @copyright 2013 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Digital Downloads Cart Widget - AJAXed
 * Plugin URI:        http://connections-pro.com
 * Description:       An Extension for Easy Digital downloads that adds a new widget that loads the downloads cart HTML via an AJAX request.
 * Version:           1.0
 * Author:            Steven A. Zahm
 * Author URI:        http://connections-pro.com
 * Text Domain:       edd_cart_ajaxed
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

class edd_Custom_Cart_Widget {

	public function __construct() {

		// Enqueue the CSS and JS.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );

		// Declare which function processes the ajax request.
		add_action( 'wp_ajax_nopriv_ajax_refresh_cart', array( __CLASS__, 'ajax_refresh_cart' ) );
		add_action( 'wp_ajax_ajax_refresh_cart', array( __CLASS__, 'ajax_refresh_cart' ) );

		// Register the widget.
		add_action( 'widgets_init', array( __CLASS__, 'edd_cart_widget_ajaxed' ) );
	}

	/**
	 * Enqueue the plugins CSS and JS files.
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @return void
	 */
	public static function enqueueScripts() {

		wp_enqueue_script( 'eddajaxcartwidget', plugin_dir_url( __FILE__ ) . 'assets/js/edd-cart-ajax.js', array( 'jquery', 'edd-ajax' ), '1.0', true );
		wp_enqueue_style( 'eddajaxcartwidget', plugin_dir_url( __FILE__ ) . 'assets/css/edd-cart-ajaxed.css', array(), '1.0' );
	}

	/**
	 * Register the widget.
	 *
	 * @access  private
	 * @since  1.0
	 *
	 * @return void
	 */
	public static function edd_cart_widget_ajaxed() {

		register_widget( 'edd_cart_widget_ajaxed' );
	}

	/**
	 * Return the current cart contents HTML.
	 *
	 * @access  private
	 * @since  1.0
	 * @uses   edd_shopping_cart()
	 *
	 * @return string JSON encoded cart contents.
	 */
	public static function ajax_refresh_cart() {

		// Generate and encode the response.
		$response = json_encode(
			array(
				'widget'  => '.widget.widget_edd_cart_ajaxed .widget_edd_cart_ajaxed-response',
				'content' => edd_shopping_cart( false ),
			)
		);

		// Output the JSON response.
		header( "Content-Type: application/json" );
		echo $response;
		exit;
	}
}

function edd_Custom_Cart_Widget() {

	return new edd_Custom_Cart_Widget();
}

// Start the plugin. This should ensure that EDD is loaded first.
add_action( 'plugins_loaded', 'edd_Custom_Cart_Widget', 11 );


class edd_cart_widget_ajaxed extends WP_Widget {

	function edd_cart_widget_ajaxed() {

		parent::WP_Widget( false, __( 'Downloads Cart AJAXed', 'edd_cart_ajaxed' ), array( 'description' => __( 'Display an AJAXed downloads shopping cart', 'edd_cart_ajaxed' ) ) );
	}

	/*
	 * Display the cart loading message and spinner while waiting for the AJAX refresh.
	 */
	function widget($args, $instance) {

		extract( $args );
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );

		global $post, $edd_options;

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		do_action( 'edd_before_cart_widget' );
		// edd_shopping_cart( true );

		?>

		<div class="widget_edd_cart_ajaxed-response">

			<div class="widget_edd_cart_ajaxed-spinner-wave">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>

			<p class="widget_edd_cart_ajaxed-loading_message"><?php _e( 'The cart is loading...', 'edd_cart_ajaxed' ) ?></p>

		</div>

		<?php

		do_action( 'edd_after_cart_widget' );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quantity'] = isset( $new_instance['quantity'] ) ? strip_tags( $new_instance['quantity'] ) : '';
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			 name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>

		<?php
	}

}
