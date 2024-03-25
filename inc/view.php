<?php
session_start();
include_once 'helpers/header.php';

class StoreSelectionView
{
	//formular de selectie magazin
	public static function show_form($store_list)
	{
		if ($_SESSION['user'] !== NULL) {
			global $head;
			$output = $head;
			$output .= '
				<form  align ="center" action=" /wordpress/magazine/" method="POST" >
					<table style= "width:400px"  align ="center"class = "table table-hover table-sm table-striped  table-success" id="Table2" border="1">
						<thead class="thead-dark">
							<tr>
								<th > id</th>
								<th > retea</th>
								<th > magazin</th>
								<th > select</th>
							</tr>
						</thead >';

			foreach ($store_list as $item) {
				$id = $item['id'];
				$network = $item['retea'];
				$store = $item['magazin'];


				$output .=
					<<<DOC
					<tr>
						<td> <input type="text"   name = "id_mag[]" value="$id"; readonly ></td>
					
						<td> <input type="text"   name = "retea[]" value="$network"; readonly ></td>
						
						<td> <input style= "width:200px" type="text" name = "store[]" value="$store"; readonly ></td>

						<td> <input type="radio" name="store_id" value="$id";  ></td> 
				
					</tr>
				DOC;
			}


			$output .=
				<<<DOC
				</table> 

				<input type="submit" class="btn btn-success" name"select_magazin" value="Go to Forms" >

				</form>
			DOC;

			if (isset($_POST['store_id']) && is_numeric($_POST['store_id'])) {
				$_SESSION['store_id'] = $_POST['store_id'];
				header('Location:/wordpress/formular/ ');
			}
		} else {
			$output = 'Este necesar autentificarea';
		}
		return $output;
	}
}

//pagina formular
class FormsPageView
{
	public static function show_table($form_abg, $form_competitor, $store_name_array)
	{
		if ($_SESSION['user'] !== NULL) {
			global $head;
			$output = $head;
			$user_id = $_SESSION['user_id'];
			$user_name = $_SESSION['user'];
			$store_id = $_SESSION['store_id'];
			$store_name = end($store_name_array);
			$store_name_string = end($store_name);
			$_SESSION['store_name_string'] = $store_name_string;
			$date = $_SESSION['date'];

			$array_category = array();
			$array_brand = array();
			foreach ($form_abg as $row) {
				$array_category[] = $row['categorie'];
				$array_brand[] = $row['brand'];
			}
			$unique_array_category = array_unique($array_category);
			$unique_array_brand = array_unique($array_brand, SORT_STRING | SORT_FLAG_CASE);


			$output .=
				'<div class="container" >
						
					<div class="selectori" align ="right">
						<input style="width:180px" type = "text" id="SelectByCod" onkeyup="SelectByCod();
						"placeholder="cod ex: BBIM13300XM">
							<br><br>

						 	Brand:<select id="SelectByBrand" onchange="SelectByBrand()">
								<option value="">ALL</option>';
								foreach ($unique_array_brand as $value) {
									$brand = $value;
									$output .= <<<DOC
													<option value= $brand > $brand</option>
													DOC;
									}
				$output .= '</select>

							<br><br>
							Categorie:<select id="SelectByCategory" onchange="SelectByCategory()">
								<option value="">ALL</option>';
			foreach ($unique_array_category as $value) {
				$category = $value;
				$output .= <<<DOC
														<option value= $category > $category</option>
														DOC;
			}

			$output .= '</select> </div>';



			$output .= '<div  style="overflow-x:auto;">
							<form  action="/wordpress/formular/ " method="POST" 
								onsubmit="return submitForm(this);">';
			//info about curent session : user/magazin/date

			$output .= <<<DOC
							<input style="width:60px"type="text"   name = "user_name" value=" $user_name"; readonly >
							<input  style="width:160px" type="text"   name = "store_name" value="$store_name_string"; readonly >
							<input style="width:120px"type="text"   name = "date" value=" $date"; readonly ><br>
							<input type="hidden"   name = "user_id" value=" $user_id"; readonly >
							<input type="hidden"   name = "store_id" value=" $store_id"; readonly >
							DOC;

			//form competitors
			$output .= '<table class="table table-hoover table-sm table-success" id="Table2">
							<tr class="thead-dark">
								<th hidden> Id</th>
								<th > Brand</th>
								<th > Categ</th>
								<th > Cant</th>
								<th > Add</th>
								<th > Sub</th>
							</tr>';


			foreach ($form_competitor as $row) {
				$id = $row['id'];
				$categorie = $row['categorie'];
				$brand = $row['brand'];

				$output .= '<tr">';
				$output .= <<<DOC
								<td hidden> <input style="width:30px" type="text"  class="clasaId2" name = "id_competitor[]" value="$id"; readonly ></td>
				
								<td>  <b>$brand </b></td>
					
								<td> <b>$categorie</b></td>
				
								<td> <input style="width:40px" class = "classCant" inputmode="numeric" pattern="[0-9]*" type="number"   name = "qty_competitor[]" value="" ></td>
					
								<td> <button style="width:50px" type="button" class="btnAdd">+</button></td>
			
								<td> <button type="button" class="btnSub">-</button></td>
								DOC;
				$output .= '</tr>';
			}

			$output .= '</table>   
			
			</div>';


			//form ABG
			$output .= '<div  style="overflow-x:auto;"> 
						<table class="table table-hoover table-sm table-success" id="TableABG" >
							<tr class="thead-dark">
								<th hidden >id</th>
								<th > Brand</th>
								<th > Categ</th>
								<th > Cod</th>
								<th > Cant</th>
								<th > Add</th>
								<th > Sub</th>
							</tr>';

			foreach ($form_abg as $row) {
				$id = $row["id"];
				$category = $row['categorie'];
				$brand = $row['brand'];
				$cod = $row['cod'];

				$output .= <<<DOC
								<tr  >
									<td hidden> <input style="width:30px"; type="text"  class="clasaId" name = "id_abg[]" value="$id"; readonly ></td>
						
									<td> <b>$brand</b></td>
						
									<td> <b>$category</b></td>
						
									<td> <b>$cod</b></td>
					
									<td> <input  style="width:40px" class="classCant" inputmode="numeric" pattern="[0-9]*" type="number"  name = "qty_abg[]" value= "" ; ></td>
						
									<td> <button style="width:50px" type="button" class="btnAdd">+</button></td>
					
									<td> <button type="button" class="btnSub">-</button></td>
								</tr>
							DOC;
			}

			$output .= '</table>
					</div><input type = "submit"  class="btn btn-success" name = "send" value = "Send Data">
					</form> 
					</div> ';

			return $output;
		} else {
			echo 'Este necesar autentificarea';
		}
	}
}
class GeneralTableView
{


	public static function show_general_table($general_info)
	{
		if ($_SESSION['user'] !== null) {

			global $head;
			$output = $head;

			$array_categorie = [];
			$array_brand = [];
			$array_user = [];


			foreach ($general_info as $row) {

				$array_categorie[] = $row['categorie'];
				$array_brand[] = $row['brand'];
				$array_user[] = $row['user'];
			}

			$unique_array_categorie = array_unique($array_categorie, SORT_STRING | SORT_FLAG_CASE);

			$unique_array_brand = array_unique($array_brand, SORT_STRING | SORT_FLAG_CASE);

			$unique_array_user = array_unique($array_user, SORT_STRING | SORT_FLAG_CASE);
			$start_date = isset($_POST['selectDateStart']) ? htmlspecialchars($_POST['selectDateStart'], ENT_QUOTES) : '';
			$end_date = isset($_POST['selectDateEnd']) ? htmlspecialchars($_POST['selectDateEnd'], ENT_QUOTES) : '';

			$output .= '<div class = "selectori-final">';
			$output .= '<div><button onclick="ExportToExcel();" class="btn btn-primary">Export table in Excel</button> <br><br>';
			$output .= '<button  class="btn btn-danger" onclick="return stergereLocalStorage();" ">Clear All</button> 
						<br><br>
							User:<select id="User" onchange="selectUser();">
								<option value="">ALL</option>';
								foreach ($unique_array_user as $value) {
									$user = $value;
									$output .= <<<DOC
												<option value= $user > $user</option>
												DOC;
								}

			$output .= '</select>
							<br><br>
                                Brand:<select id="Brand" onchange="selectBrand();">
                            <option value="">ALL</option>';
							foreach ($unique_array_brand as $value) {
								$brand = $value;
								$output .= <<<DOC
													<option value= $brand > $brand</option>
													DOC;
							}
			
			$output .= '</select>
					</div> ';

			$output .= '<div >Cauta magazinul:<input style = "width: 150px" type = "text" id="selectMagazin" onkeyup="selectMagazin();" 
					placeholder="ex:Altex Era"> <br><br>';

			$output .= '<form action="/wordpress/final-view/  " method="POST">';

			$output .= <<<DOC
							Data start:<input style = "width:150px" type = "date" name ="selectDateStart" id="selectDate"  value = $start_date><br><br>
		
							Data Stop: <input style = "width:150px"type = "date" name ="selectDateEnd" id="selectDate2" value = $end_date>
						DOC;

			$output .= '<input style = "width: 150px" type = "submit"  class="btn btn-success" value = "Selecteaza Perioada">
							</form><br>
                           
							Categorie:<select id="Categorie" onchange="selectCategorie();">
								<option value="">ALL</option>';
								foreach ($unique_array_categorie as $value) {
									$category = $value;
									$output .= <<<DOC
								<option value= $category > $category</option>
								DOC;
								}
			
			$output .= '</select>  </div> </div>';
			//Tabel final view
			$output .= '<div class = "final-view" style="overflow-y:auto;">';


			$output .= '<table class = "table table-hover table-sm table-striped " id="finalTable" border="3"">

					<thead class="thead-dark">		
					<tr>
						<th style="width: 100px"> Network</th>
						<th style="width: 100px"> Shop</th>
						<th style="width: 100px"> Category</th>
						<th style="width: 100px"> Brand</th>
						<th style="width: 100px"> Product code</th>
						<th style="width: 100px"> Energy class</th>
						<th style="width: 100px"> Beyond/Prologue</th>
						<th style="width: 50px"> Present display</th>
						<th style="width: 50px">Data</th>
						<th style="width: 50px"> User</th>
					</tr>
                    </thead>';
			//var_dump($results);


			foreach ($general_info as $row) {
				$network = $row['retea'];
				$shop = $row['magazin'];
				$categorie = $row['categorie'];
				$brand = $row['brand'];
				$cod_produs = $row['cod'];
				$clasa_energetica = $row['energy_class'];
				$series = $row['serie'];
				$prezenta_display = $row['cantitate_abg'];
				$data = $row['data'];
				$user = $row['user'];

				$output .= <<<DOC
							<tr class="table-success">
								<td id = "network" class="Network"  > $network </td>
								
								<td class="shop">  $shop</td>
							
								<td class = "Categorie">  $categorie </td>
							
								<td class = "Brand"> $brand </td>
							
								<td class="Cod">$cod_produs</td>
							
								<td class="clasaEnergetica">$clasa_energetica</td>
							
								<td class = "Series"> $series </td>
							
								<td class = "ClasaCant"> $prezenta_display</td>
						
								<td class="Date">  $data</td>
						
								<td class="User"> $user</td>
							</tr>
						DOC;
			}
			$output .= '</table>
						</div> <hr>';

			return $output;
		} else {
			echo "este necesara autentificarea";
		}
	}
}

class ReportView
{
	public static function show_report($report, $users) 
	{

		if ($_SESSION['user'] !== null) {
			global $head;
			$output = $head;

			$user_array = array();
			foreach ($users as $value) {
				$user_array[] = $value['user'];
			}

			$unique_array_user = array_unique($user_array, SORT_STRING | SORT_FLAG_CASE);
			$start_date = isset($_POST['selectDateStart']) ? htmlspecialchars($_POST['selectDateStart'], ENT_QUOTES) : '';
			$end_date = isset($_POST['selectDateEnd']) ? htmlspecialchars($_POST['selectDateEnd'], ENT_QUOTES) : '';
			$output .= '<button  class="btn btn-primary" onclick="ExportToExcel();">Export table in Excel</button> <br> <br>

						<form action="/wordpress/total-user/" method="POST">';

			$output .= <<<DOC
							Data start:<input style = "width:160px" type = "date" 
							name ="selectDateStart" id="selectDate"  value = $start_date>
							DOC;

			$output .= <<<DOC
							Data Stop: <input style = "width:250px"type = "date" name ="selectDateEnd" id="selectDate2" value = $end_date> <br><br>
			
								User:<select id="User" name = "user">
									<option value="All">ALL</option>	 
							DOC;

							foreach ($unique_array_user as $value) {
								$output .= <<<DOC
												<option value = $value  
												DOC;
								if (isset($_POST['user']) && ($_POST['user'] === $value)) {

									$output .= "selected";
								}

								$output .= ">$value</option>";

								$output .= ">$value</option>";
							}
			$output .= '</select>';

			$store = $_SESSION['store_name_string'];

			$output .= <<<DOC
							<input style = "width:250px" type = "submit"  class="btn btn-success" name ="calculateUser"  value = "Calcul pe User"> <br><br>
			
							Magazin:<input style = "width:260px" type = "text" name ="store"   value = "$store" >
							
							<input style = "width:250px" type = "submit" class="btn btn-success" name ="calculateStore"  value = "Calcul pe Magazin"> <br><br>
		
							</form> <br> <br>
						
						<div class = "finalView" style="overflow-x:auto;">

								<table class = "table table-hover table-sm table-striped " id="finalTable" border="3">

									<thead class="thead-dark">	
										<tr>
											<!-- <th hidden style="width: 100px"> NETWORK</th>
											<th hidden style="width: 100px"> SHOP</th> -->
											<th style="width: 100px"> BRAND</th>
											<th style="width: 100px"> CATEGORIE</th>
											<th style="width: 50px"> Total </th>
											<!-- <th hidden style="width: 50px">DATA</th>
											<th style="width: 50px"> USER</th> -->
										</tr>
									</thead>
						DOC;

			if (!empty($report)) {
				foreach ($report as $row) {
					$brand = $row['brand'];
					$category = $row['categorie'];
					$total = $row['Total'];
					$output .= '';

					$output .= <<<DOC
							<tr class="table-success">
								<td> $brand </td>
								<td class = "Categorie">  $category </td>
								<td class = "ClasaCant"> $total</td>
							</tr>
						DOC;
				}
			}


			$output .= '</table>';

			return $output;
		} else {

			echo "este necesara autentificarea";
		}
	}
}
