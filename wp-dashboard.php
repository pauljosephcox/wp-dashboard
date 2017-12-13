<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name: WP Dashboard
 * Description: A Custom Wordpress Dashboard
 * Author: Paul Joseph Cox
 * Version: 1.0
 * Author URI: http://pauljosephcox.com/
 */


if (!defined('ABSPATH')) exit;


class CoxyDashboard {

	public $errors = false;
	public $notices = false;
	public $slug = 'coxy-dashboard';
	public $title = 'Dashboard';

	function __construct() {

		$this->path = plugin_dir_path(__FILE__);
		$this->folder = basename($this->path);
		$this->dir = plugin_dir_url(__FILE__);
		$this->version = '1.0';

		$this->errors = false;
		$this->notice = false;

		// Actions
		if(is_admin()){

			add_action('init', array($this, 'setup'), 10, 0);
			add_action('admin_enqueue_scripts', array($this, 'scripts'));

		}

	}

	/**
	 * Setup The Plugin
	 * @return null
	 */

	public function setup() {

		add_filter( 'admin_title', array( $this, 'page_title' ), 10, 2 );
        add_action( 'admin_menu', array( $this, 'register_options_page' ) );
        add_action( 'current_screen', array( $this, 'redirect' ) );

	}

	/**
	 * Add Custom scriptions
	 * @return type
	 */

	public function scripts() {

		wp_enqueue_style('coxy_dashbaord_css', $this->dir.'/assets/dashboard.css', array(), $this->version, true);
		wp_enqueue_script('coxy_dashbaord_js', $this->dir.'/assets/dashboard.js', array(), $this->version, true);

	}

	/**
	 * Page Title
	 * @param string $admin_title
	 * @param string $title
	 * @return string
	 */

	public function page_title($admin_title, $title){

		global $pagenow;
        if( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $this->slug == $_GET['page'] ) $admin_title = $this->title;
        return $admin_title;

	}

	/**
	 * Register an options page
	 * @return null
	 */

	public function register_options_page() {

		global $parent_file;
		global $submenu_file;
		global $menu;
		global $submenu;

		// Add & Remove Pages
		add_menu_page('Coxy Dashboard', 'Coxy Dashboard', 'read', $this->slug, function(){ $this->template_include('dashboard.php'); });
		remove_menu_page($this->slug);

		// Set Active Menu Item
		$parent_file = 'index.php';
        $submenu_file = 'index.php';

        // Name Menu Item
        $menu[2][0] = ($this->title) ? $this->title : __('Dashboard');
        $submenu['index.php'][0][0] = $this->title;


	}


	/**
	 * Which Template
	 * @param string $template
	 * @return string
	 */

	public function template($filename) {

		// check theme
		$theme = get_template_directory() . '/'.$this->slug.'/' . $filename;

		if (file_exists($theme)) {
			$path = $theme;
		} else {
			$path = $this->path . 'templates/' . $filename;
		}
		return $path;

	}


	/**
	 * Include a Template
	 * @param string $template
	 * @param * $data
	 * @param string $name
	 * @return null
	 */

	public function template_include($template,$data = null,$name = null){

		if(isset($name)){ ${$name} = $data; }
		$path = $this->template($template);
		include($path);
	}


	/**
	 * Redirect
	 * @param string $path
	 * @return null
	 */

	public function redirect($path) {

		if($path->id == 'dashboard') {
			wp_safe_redirect( 'admin.php?page='.$this->slug );
	  		exit();
	  	}

	}


}


// ------------------------------------
// Go
// ------------------------------------

$coxy_dashboard = new CoxyDashboard();
