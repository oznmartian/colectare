<?php
//user and store selection
class UserAndStoreSelection
{

	private $db_conn;
	public function __construct($db)
	{

		$this->db_conn = $db;
	}

	public function get_user_and_store()
	{
		$curent_user = wp_get_current_user();
		$user_login = $curent_user->user_login;
		$user_id = $curent_user->ID;

		$_SESSION['user_id'] = $user_id;
		$_SESSION['user'] = $user_login;

		//get stores by user id

		if (!empty($user_login)) {
			$table = $this->db_conn->prefix . 'date_magazine';

			$query =  $this->db_conn->prepare("SELECT * FROM $table WHERE status = %s AND user = %s ", 'activ', $user_login);
			//$query ="SELECT * FROM $table WHERE statuts = 'activ' AND user = '".$user_login."' ";
			//var_dump($query);

			//$this->db_conn->show_errors();
			$result = $this->db_conn->get_results($query, ARRAY_A);
			//$this->db_conn->print_error();

			return $result;
		}
	}
}



//saving data 
class GetFormsModel
{
	private $id_magazin;
	private $db_conn;

	public function __construct($db, $store_id)
	{


		$this->db_conn = $db;
		$this->id_magazin = $store_id;
	}

	public function get_store_name()
	{


		$f_store_id = $this->id_magazin;

		$table = $this->db_conn->prefix . 'date_magazine';

		$query =  $this->db_conn->prepare("SELECT magazin FROM $table WHERE id = %d", $f_store_id);
		//$this->db_conn->show_errors();

		$store_name_array = $this->db_conn->get_results($query, ARRAY_A);
		//$this->db_conn->print_error();

		return $store_name_array;
	}


	public function get_table_abg()
	{
		//Select from table lista_abg
		$table_ABG = $this->db_conn->prefix . 'lista_abg';

		//$query_str =  $this->db_conn->prepare("SELECT id, categorie, brand, cod FROM $table_ABG WHERE status = %s ORDER BY categorie, brand, cod ASC, 'activ' ");
		$query_str = "SELECT id, categorie, brand, cod FROM $table_ABG ORDER BY cod DESC";
		$form_abg = $this->db_conn->get_results($query_str, ARRAY_A);

		return $form_abg;
	}
	public function get_table_competitor()
	{ //select from table date_concurenta
		$query_str = "SELECT id, brand, categorie  FROM wp_date_concurenta";

		$form_competitor = $this->db_conn->get_results($query_str, ARRAY_A);

		return $form_competitor;
	}
}

class SaveDataModel
{
	public static function save_data($input, $db_conn)
	{
		//var_dump($input);



		if (
			isset($input['id_abg']) && isset($input['qty_abg']) &&
			isset($input['user_id']) && isset($input['store_id']) &&
			isset($input['date'])
			&& isset($input['id_competitor']) && isset($input['qty_competitor'])
		) {


			$id_abg = $input['id_abg'];
			$qty_abg = $input['qty_abg'];
			$user = $input['user_id'];
			$store_id = $input['store_id'];
			$date = $input['date'];
			$id_competitor = $input['id_competitor'];
			$qty_competitor = $input['qty_competitor'];

			if ($user !== NULL && $id_abg !== NULL && $store_id !== NULL && $qty_abg) {
				for ($index = 0; $index < count($qty_abg); $index++) {
					$f_id_abg = $id_abg[$index];
					$f_qty_abg = $qty_abg[$index];
					$f_user_id = $user;
					$f_store_id = $store_id;
					$f_date = $date;

					if (!empty($f_qty_abg)) {

						$table = $db_conn->prefix . 'colectare_abg';

						$param = array(
							'id_date_abg' => $f_id_abg,
							'cantitate_abg' => $f_qty_abg,
							'id_shop' => $f_store_id,
							'id_user' => $f_user_id,
							'data' => $f_date,

						);

						$sanitize = array(

							'%d', '%d', '%d', '%d', '%s'
						);
						//var_dump($param);
						$db_conn->query('START TRANSACTION');
						$db_conn->insert($table, $param, $sanitize);
						$db_conn->query('COMMIT');
					}
				}
			}

			if ($id_competitor !== NULL && $user !== NULL && $qty_competitor) {

				for ($i = 0; $i < count($qty_competitor); $i++) {

					$f_id_competitor = $id_competitor[$i];
					$f_qty_competitor = $qty_competitor[$i];
					$f_user_id = $user;
					$f_store_id = $store_id;
					$f_date = $date;

					if (!empty($f_qty_competitor)) {

						$table2 = $db_conn->prefix . 'colectare_concurenta';

						$param2 = array(

							'cantitate_con' => $f_qty_competitor,
							'id_date_con' => $f_id_competitor,
							'id_shop' => $f_store_id,
							'id_user' => $f_user_id,
							'data' => $f_date,

						);

						$sanitize2 = array(

							'%d', '%d', '%d', '%d', '%s'

						);

						$db_conn->query('START TRANSACTION');
						$db_conn->insert($table2, $param2, $sanitize2);
						$db_conn->query('COMMIT');
					}
				}

				$noticemsg = "";
				if (!empty($param2) && !empty($param)) {
					$noticemsg .= 'Datele din tabele s-au salvat';
				} elseif (empty($param2)) {
					$noticemsg .= 'va rugam sa introduceti date in tabelul concurenta! <br>
						Atentie!!! <br>
						Datele din tabelul ABG  au fost salvate';
					echo $noticemsg;
				} elseif (empty($param)) {
					$noticemsg .= 'va rugam sa introduceti date in tabelul ABG! <br>
						Atentie!!! <br>
						Datele din tabelul Concurenta  au fost salvate';
					echo $noticemsg;
				}
				$_SESSION['mesaj'] = $noticemsg;

				//redirect to final view if data were saved
				if ($noticemsg === 'Datele din tabele s-au salvat') {


					(header('Location:/wordpress/final-view/'));
				}
			}
		}
	}
}
//final view page
class GeneralTableModel
{
	private $input;
	private $db_conn;

	public function __construct($a, $b)
	{
		$this->input = $a;
		$this->db_conn = $b;
	}

	public function get_general_table()
	{
		if (isset($this->input['selectDateStart']) && isset($this->input['selectDateEnd']) && $this->input['selectDateStart'] !== NULL && $this->input['selectDateEnd'] !== NULL) {
			$dateStart = $this->input['selectDateStart'];
			$dateEnd = $this->input['selectDateEnd'];


			$query_str = $this->db_conn->prepare("
            SELECT  retea, magazin, categorie, brand, cod,  energy_class, serie, cantitate_abg, data,user FROM wp_lista_abg
            INNER JOIN 
            wp_colectare_abg ON (wp_lista_abg.id=wp_colectare_abg.id_date_abg )
            INNER JOIN 
            wp_date_magazine ON(wp_colectare_abg.id_shop = wp_date_magazine.id)  WHERE data >= %s AND data <= %s
            union all
            SELECT retea, magazin, categorie, brand, Null as Col5, Null as Col6, Null as Col7,cantitate_con, data,user  FROM wp_date_concurenta
            INNER JOIN 
            wp_colectare_concurenta ON (wp_date_concurenta.id=wp_colectare_concurenta.id_date_con )
            INNER JOIN
            wp_date_magazine ON (wp_date_magazine.id=wp_colectare_concurenta.id_shop)  WHERE data >= %s AND data <= %s  ORDER BY data DESC, magazin, categorie, brand ASC", $dateStart, $dateEnd, $dateStart, $dateEnd);


			$general_info = $this->db_conn->get_results($query_str, ARRAY_A);

			return $general_info;
		} else {
			$query_str = "
            SELECT  retea, magazin, categorie, brand, cod,  energy_class, serie, cantitate_abg, data,user FROM wp_lista_abg
            INNER JOIN
            wp_colectare_abg ON (wp_lista_abg.id=wp_colectare_abg.id_date_abg )
            INNER JOIN
            wp_date_magazine ON(wp_colectare_abg.id_shop = wp_date_magazine.id)
            union all
            SELECT retea, magazin, categorie, brand, Null as Col5, Null as Col6, Null as Col7,cantitate_con, data,user  FROM wp_date_concurenta
            INNER JOIN
            wp_colectare_concurenta ON (wp_date_concurenta.id=wp_colectare_concurenta.id_date_con )
            INNER JOIN
            wp_date_magazine ON (wp_date_magazine.id=wp_colectare_concurenta.id_shop) ORDER BY   data DESC, magazin, categorie, brand ASC";


			$general_info = $this->db_conn->get_results($query_str, ARRAY_A);


			return $general_info;
		}
	}
}


class ReportModel
{

	private $input;
	private $db_conn;


	public function __construct($a, $b)
	{

		$this->input = $a;
		$this->db_conn = $b;
	}

	public static function get_users($db_con)
	{

		$query_str = "SELECT user FROM wp_date_magazine";
		//var_dump($query_str);
		//$this->db_conn->show_errors();
		$users = $db_con->get_results($query_str, ARRAY_A);
		//$this->db_conn->print_error();
		return $users;
	}

	public function generate_report()
	{

		if (isset($this->input['calculateUser']) && !empty($this->input['selectDateStart']) && !empty($this->input['selectDateEnd']) && !empty($this->input['user'])) {
			$dateStart = $this->input['selectDateStart'];
			$dateEnd = $this->input['selectDateEnd'];
			$user = $this->input['user'];
			$network = "TEST";

			$query_str = $this->db_conn->prepare(
				"SELECT  retea, categorie, brand, data, user, ordine, ordine_categorie, SUM(cantitate_abg) as Total FROM wp_lista_abg
				INNER JOIN 
				wp_colectare_abg ON (wp_lista_abg.id=wp_colectare_abg.id_date_abg )
				INNER JOIN 
				wp_date_magazine ON(wp_colectare_abg.id_shop = wp_date_magazine.id) WHERE  data>= %s AND data <=%s  AND user =%s AND retea <>%s GROUP BY brand, categorie
				union all
				SELECT retea, categorie, brand,  data,user, ordine, ordine_categorie, SUM(cantitate_con) as Total FROM wp_date_concurenta
				INNER JOIN 
				wp_colectare_concurenta ON (wp_date_concurenta.id=wp_colectare_concurenta.id_date_con )
				INNER JOIN
				wp_date_magazine ON (wp_date_magazine.id=wp_colectare_concurenta.id_shop)  WHERE data>= %s AND data <=%s AND user =%s AND retea <>%s GROUP BY brand, categorie ORDER BY ordine_categorie, ordine",
				array($dateStart, $dateEnd, $user, $network, $dateStart, $dateEnd, $user, $network)
			);


			$report = $this->db_conn->get_results($query_str, ARRAY_A);
			return $report;
		} elseif (isset($this->input['calculateStore']) && !empty($this->input['selectDateStart']) && !empty($this->input['selectDateEnd']) && !empty($this->input['store'])) {
			$dateStart = $this->input['selectDateStart'];
			$dateEnd = $this->input['selectDateEnd'];
			$store = $this->input['store'];
			$network = "TEST";

			$query_str = $this->db_conn->prepare(
				"SELECT retea, magazin, categorie, brand, data, user, ordine, ordine_categorie, SUM(cantitate_abg) as Total FROM wp_lista_abg
				INNER JOIN 
				wp_colectare_abg ON (wp_lista_abg.id=wp_colectare_abg.id_date_abg )
				INNER JOIN 
				wp_date_magazine ON(wp_colectare_abg.id_shop = wp_date_magazine.id) WHERE    data>= %s AND data <=%s  AND magazin =%s AND retea <>%s GROUP BY brand, categorie
				union all
				SELECT retea,magazin, categorie, brand, data,user, ordine, ordine_categorie, SUM(cantitate_con) as Total FROM wp_date_concurenta
				INNER JOIN 
				wp_colectare_concurenta ON (wp_date_concurenta.id=wp_colectare_concurenta.id_date_con )
				INNER JOIN
				wp_date_magazine ON (wp_date_magazine.id=wp_colectare_concurenta.id_shop)  WHERE    data>= %s AND data <=%s AND magazin =%s AND retea <>%s GROUP BY brand, categorie ORDER BY ordine_categorie, ordine",
				array($dateStart, $dateEnd, $store, $network, $dateStart, $dateEnd, $store, $network)
			);


			//var_dump($query_str);
			//$this->db_conn->show_errors();
			$report = $this->db_conn->get_results($query_str, ARRAY_A);
			//$this->db_conn->print_error();

			return $report;
		}
	}
}
