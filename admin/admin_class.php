<?php
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	public function login2() {
		extract($_POST);
		$email = $this->db->real_escape_string($email);
		$qry = $this->db->query("SELECT * FROM customers WHERE email = '$email'");
	
		if ($qry->num_rows > 0) {
			$row = $qry->fetch_assoc();
			if (password_verify($password, $row['password'])) {
				$_SESSION['login_id'] = $row['id'];
				return 1; // Success
			} else {
				return 0; // Incorrect password
			}
		} else {
			return 0; // User not found
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = '$type' ";
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	public function signup() {
		extract($_POST);
		$password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
		$email = $this->db->real_escape_string($email); // Sanitize email input
		$name = $this->db->real_escape_string($name); // Sanitize name input
		$contact = $this->db->real_escape_string($contact); // Sanitize contact input
		$address = $this->db->real_escape_string($address); // Sanitize address input

		$qry = $this->db->query("INSERT INTO customers (name, contact, address, email, password) VALUES ('$name', '$contact', '$address', '$email', '$password')");
		if ($qry) {
			return 1; // Success
		} else {
			return 0; // Error
		}
	}

	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}
	function save_page_img(){
		extract($_POST);
		if($_FILES['img']['tmp_name'] != ''){
				$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
				if($move){
					$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
					$hostName = $_SERVER['HTTP_HOST'];
						$path =explode('/',$_SERVER['PHP_SELF']);
						$currentPath = '/'.$path[1]; 
   						 // $pathInfo = pathinfo($currentPath); 

					return json_encode(array('link'=>$protocol.'://'.$hostName.$currentPath.'/admin/assets/uploads/'.$fname));

				}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['system'][$key] = $value;
		}

			return 1;
				}
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM categories where name ='$name' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO categories set $data");
		}else{
			$save = $this->db->query("UPDATE categories set $data where id = $id");
		}

		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM categories where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_book(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','category_ids','price')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$data .= ", price = '".str_replace(',','',$price)."' ";
		$data .= ", category_ids = '".implode(',',$category_ids)."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", image_path = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO books set $data");
		}else{
			$save = $this->db->query("UPDATE books set $data where id = $id");
		}

		if($save)
			return 1;
	}
	function delete_book(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM books where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function add_to_cart(){
		extract($_POST);
		$data = " customer_id = {$_SESSION['login_id']} ";
		$data .= ", book_id = $book_id ";
		$data .= ", qty= $qty";
		$data .= ", price= $price";
		$check = $this->db->query("SELECT * FROM cart where book_id = $book_id and customer_id ={$_SESSION['login_id']} ");
		$id= $check->num_rows > 0 ? $check->fetch_array()['id'] : '';
		if(!empty($id))
			$save = $this->db->query("UPDATE cart set qty = qty+$qty where id = $id");
		else
			$save = $this->db->query("INSERT INTO cart set $data");
		if($save){
			return 1;
		}
	}
	function get_cart_count(){
		$qry = $this->db->query("SELECT c.*,b.image_path,b.title FROM cart c inner join books b on b.id = c.book_id where c.customer_id ={$_SESSION['login_id']}");
		$data = array();
		$count = 0 ; 
		$data['list']=array();
		while ($row=$qry->fetch_array()) {
			$data['list'][]=$row;
			$count += $row['qty'];
		}
		$data['count'] = $count;
		return json_encode($data);
	}
	function update_cart(){
		extract($_POST);
		$save = $this->db->query("UPDATE cart set qty = $qty where id = $id");
		if($save){
			return 1;
		}

	}
	function delete_cart(){
		extract($_POST);
		$del = $this->db->query("DELETE FROM cart where id =  $id");
		if($del)
			return 1;
	}
	function save_order(){
		extract($_POST);
		$data = " customer_id = {$_SESSION['login_id']} ";
		$data .= ", address = '$address' ";
		$save = $this->db->query("INSERT INTO orders set $data");
		if($save){
			$id = $this->db->insert_id;
			$qry = $this->db->query("SELECT * FROM cart where customer_id ={$_SESSION['login_id']}");
			while($row = $qry->fetch_array()){
				$data = " order_id = $id ";
				$data .= ", book_id = {$row['book_id']} ";
				$data .= ", qty = {$row['qty']} ";
				$data .= ", price = '{$row['price']}'";
				if($order[] = $this->db->query("INSERT INTO order_list set $data")){
					$this->db->query("DELETE FROM cart where id ='{$row['id']}' ");
				}
			}
			if(isset($order))
				return 1;
		}
	}
	function update_order(){
		extract($_POST);
		$save = $this->db->query("UPDATE orders set status = $status where id = $id");
		if($save)
			return 1;

	}
	function delete_order(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM orders where id = ".$id);
		$delete2 = $this->db->query("DELETE FROM order_list where order_id = ".$id);
		if($delete && $delete2){
			return 1;
		}
	}
}