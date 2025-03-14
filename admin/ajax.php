<?php
ob_start();
session_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

function login2() {
    extract($_POST);
    $email = $this->db->real_escape_string($email);
    $qry = $this->db->query("SELECT * FROM customers WHERE email = '$email'");

    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['login_id'] = $row['id']; // Set session
            error_log("Session set for user ID: " . $row['id']); // Debugging
            return 1; // Success
        } else {
            error_log("Password verification failed for email: " . $email); // Debugging
            return 0; // Incorrect password
        }
    } else {
        error_log("User not found for email: " . $email); // Debugging
        return 0; // User not found
    }
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'save_page_img'){
	$save = $crud->save_page_img();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'update_account'){
	$save = $crud->update_account();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_category"){
	$save = $crud->save_category();
	if($save)
		echo $save;
}
if($action == "delete_category"){
	$delete = $crud->delete_category();
	if($delete)
		echo $delete;
}
if($action == "save_book"){
	$save = $crud->save_book();
	if($save)
		echo $save;
}
if($action == "delete_book"){
	$delete = $crud->delete_book();
	if($delete)
		echo $delete;
}
if($action == "save_student"){
	$save = $crud->save_student();
	if($save)
		echo $save;
}
if($action == "delete_student"){
	$delete = $crud->delete_student();
	if($delete)
		echo $delete;
}

if($action == "add_to_cart"){
	$save = $crud->add_to_cart();
	if($save)
		echo $save;
}
if($action == "get_cart_count"){
	$get = $crud->get_cart_count();
	if($get)
		echo $get;
}
if($action == "update_cart"){
	$save = $crud->update_cart();
	if($save)
		echo $save;
}
if($action == "delete_cart"){
	$delsete = $crud->delete_cart();
	if($delsete)
		echo $delsete;
}
if($action == "save_order"){
	$save = $crud->save_order();
	if($save)
		echo $save;
}
if($action == "update_order"){
	$save = $crud->update_order();
	if($save)
		echo $save;
}
if($action == "delete_order"){
	$delsete = $crud->delete_order();
	if($delsete)
		echo $delsete;
}
ob_end_flush();
?>