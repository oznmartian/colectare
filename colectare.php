<?php

/**
 *   Plugin Name: Colectare 
 *   Plugin URI: http://comanda.byethost24.com/
 *   Description: Un plugin care va activa un shortcode pentru colectare date 
 *   Version: 1.0
 *   Author: Nicolae
 *   Author URI: http://comanda.byethost24.com/
 *  
 */

// no direct access
require_once('inc/model.php');

require_once('inc/view.php');

if (!defined('ABSPATH')) {
	die;
}


function colectare_add_css()
{

	wp_register_style('colectare-css', plugins_url('/theme/css/frontend.css', __FILE__));

	wp_enqueue_style('colectare-css');
}
function colectare_add_js()
{
	wp_register_script('colectare-js', plugins_url(
		'/theme/js/frontend.js',
		__FILE__
	), array('jquery'), date("h:i:s"), true);
	wp_enqueue_script('colectare-js');
}


//tabel cu info despre magazine
register_activation_hook(__FILE__, 'table_create_magazine');
function table_create_magazine()
{

	$query_str = "CREATE TABLE wp_date_magazine (
	id INT NOT NULL AUTO_INCREMENT,
    retea VARCHAR(100)NOT NULL,
    magazin VARCHAR(100),
	user VARCHAR(100)NOT NULL,
	status VARCHAR(100) NOT NULL,
	PRIMARY KEY (id)
	)";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($query_str);
}

add_action('init', 'table_create_magazine');

//tabel cu info despre concurenta
register_activation_hook(__FILE__, 'table_create_concurenta');
function table_create_concurenta()
{

	$query_str = "CREATE TABLE wp_date_concurenta (
	id INT NOT NULL AUTO_INCREMENT,
    brand VARCHAR(100)NOT NULL,
    categorie VARCHAR(100),
    ordine INT,
    ordine_categorie INT ,
	PRIMARY KEY (id)
	)";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($query_str);
}
add_action('init', 'table_create_concurenta');

//tabelul in care se colecteaza datele de la concurenta
register_activation_hook(__FILE__, 'table_create_concurenta2');
function table_create_concurenta2()
{

	$query_str = "CREATE TABLE wp_colectare_concurenta (
		id INT NOT NULL AUTO_INCREMENT,
		id_date_con VARCHAR(100)NOT NULL,
		cantitate_con VARCHAR(100),
		id_user VARCHAR(100),
		id_shop  VARCHAR(100),
		data DATE,
		PRIMARY KEY (id)
		)";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($query_str);
}
add_action('init', 'table_create_concurenta2');

//tabelul in care se colecteaza datele ABG
register_activation_hook(__FILE__, 'table_create_abg2');
function table_create_abg2()
{

	$query_str = "CREATE TABLE wp_colectare_abg (
			id INT NOT NULL AUTO_INCREMENT,
			id_date_abg VARCHAR(100)NOT NULL,
			cantitate_abg VARCHAR(100),
			id_user VARCHAR(100),
			id_shop  VARCHAR(100),
			data DATE,
			PRIMARY KEY (id)
			)";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($query_str);
}

add_action('init', 'table_create_abg2');

//tabelul cu datele ABG
register_activation_hook(__FILE__, 'table_create_abg');
function table_create_abg()
{
	$query_str = "CREATE TABLE wp_lista_abg (
			id INT NOT NULL AUTO_INCREMENT,
			categorie VARCHAR(100) NOT NULL,
			brand VARCHAR(100) NOT NULL,
			cod_sap BIGINT ,
			cod VARCHAR(100),
			energy_class VARCHAR(10),
			serie VARCHAR(10),
            ordine INT,
            ordine_categorie INT,
			status VARCHAR (20) NOT NULL,
			PRIMARY KEY (id)
			)";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($query_str);
}
add_action('init', 'table_create_abg');

//valideaza selectia magazinului, userului si a datei de colectare
add_shortcode('select_store', 'store_selection');


function store_selection()
{

	global $wpdb;

	
	//selectie magazin de lucru	

	$model_stores_list = new UserAndStoreSelection($wpdb);
	$store_list = $model_stores_list->get_user_and_store();
	$view_store_list = StoreSelectionView::show_form($store_list);

	return $view_store_list;
}

add_shortcode('form', 'show_forms','save_data');

function show_forms()
{
	//salvare data in sesiune
	date_default_timezone_set('UTC');
	$date = new DateTimeImmutable('now', new DateTimeZone('Europe/Bucharest'));
	$_SESSION['date'] = date_format($date, 'Y-m-d');

	global $wpdb;
	$id_magazin =  isset($_SESSION['store_id']) && is_numeric($_SESSION['store_id']) ? $_SESSION['store_id'] : false;
	
	$model_send_data = new GetFormsModel( $wpdb, $id_magazin);
	$store_name_array = $model_send_data->get_store_name();
	$form_abg = $model_send_data->get_table_abg();
	$form_competitor = $model_send_data->get_table_competitor();
	
	//tabelele in pagina
	$view = FormsPageView::show_table($form_abg, $form_competitor, $store_name_array);

	return $view;
}
add_shortcode('save', 'save_data');
function save_data()
{
	global $wpdb;
	//verificare input
	$arg = [
		'date'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,

		'user_id'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,

		'store_id'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,

		'id_abg'    => array(
			'filter'    => FILTER_VALIDATE_INT,
			'flags'     => FILTER_REQUIRE_ARRAY,
		),
		'qty_abg'    => array(
			'filter'    => FILTER_VALIDATE_INT,
			'flags'     => FILTER_REQUIRE_ARRAY,
		),
		'id_competitor'    => array(
			'filter'    => FILTER_VALIDATE_INT,
			'flags'     => FILTER_REQUIRE_ARRAY,
		),
		'qty_competitor'    => array(
			'filter'    => FILTER_VALIDATE_INT,
			'flags'     => FILTER_REQUIRE_ARRAY,
		)];
		
	$input_f = filter_input_array(INPUT_POST, $arg);

	SaveDataModel::save_data($input_f, $wpdb);	
}
add_shortcode('final_view', 'general_table_view');

function general_table_view()
{
	global $wpdb;

	$arg = array(
		'selectDateStart'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,

		'selectDateEnd'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS

	);

	$input_f = filter_input_array(INPUT_POST, $arg);

	$model_general_info = new GeneralTableModel($input_f, $wpdb);
	$general_info = $model_general_info->get_general_table();
	$view = GeneralTableView::show_general_table($general_info);
	
	return $view;
}


add_shortcode('total_user', 'show_report');

function show_report() 
{

	global $wpdb;
	

	$arg = array(
		'selectDateStart'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'selectDateEnd'    =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'user'  =>  FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'calculateUser' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'store' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'calculateStore' => FILTER_SANITIZE_FULL_SPECIAL_CHARS

	);


	$input_f = filter_input_array(INPUT_POST, $arg);

	$users = ReportModel::get_users($wpdb);

	$model_report = new ReportModel($input_f, $wpdb);
	$report = $model_report->generate_report();
	
	$view = ReportView::show_report($report, $users);

	return $view;
}

//redirectionare la pagina magazine  logout
add_action('wp_logout', 'go_home');
function go_home()
{
	session_destroy();
	wp_redirect('/wordpress');
	exit();
}

//redirectionare catre pagina magazine la login
add_action('wp_login', 'go_magazine');
function go_magazine()
{

	wp_redirect('/wordpress/magazine/');
	exit();
}

add_action('wp_enqueue_scripts', 'colectare_add_css');
add_action('wp_enqueue_scripts', 'colectare_add_js');
