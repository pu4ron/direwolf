<?php
include 'config.php';
include 'functions.php';

logexists();

if((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) { //if interface was not selected
	if(($static_if == 1) && isset($static_if_index)) { // if static interface is declared in config.php
		session_start();
		$_SESSION['if'] = $static_if_index; //open session with static interface declared in config.hp
	            header('Refresh: 0; url=summary.php');
	            die();

	} else {
			header('Refresh: 0; url=summary.php');
	                die();
	}
	die();
} else { //else if inteface selected
	header('Refresh: 0; url=summary.php');
	die();
}
?>
