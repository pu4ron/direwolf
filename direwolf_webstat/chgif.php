<?php
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Direwolf dashboard - PU4RON" />
<meta name="Keywords" content="" />
<meta name="Author" content="IZ7BOJ" />
<title>Direwolf dashboard</title>
</head>
<body>
<center><font size="20"><b>Direwolf dashboard</b></font>
<h2>APRS BRASIL</h2>
<br><br><br>
</center>

<?php
include 'config.php';
include 'functions.php';

logexists();

session_start();
if((((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) and ((!isset($_GET['if'])) or (isset($_GET['if']) and ($_GET['if'] == "")))) or (isset($_GET['chgif']) and $_GET['chgif'] == "1")) //if interface was not selected
{
	$_SESSION = array();
	session_destroy(); //start session
	session_start();
?>

<form action="chgif.php" method="get">
Interface: <select name="if">

<?php
$i=0;
for ($i=0;$i<=sizeof($interfaces)-1;$i++) {
?>
	<option value=<?php echo $interfaces[$i] ?>><?php echo $interfaces[$i]." - ".$intdesc[$i] ?></option>
<?php
}
?>

</select>
<br><br><br><br><br>
<input type="submit" value="OK">
</form>

<?php
} else {
	if(!isset($_SESSION['if'])) //if now there is "if" variable
	{
		$_SESSION['if'] = $_GET['if'];
	}
	header('Refresh: 0; url=summary.php');
	die();
}
?>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
      <center><a>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</center>

</body>
</html>
