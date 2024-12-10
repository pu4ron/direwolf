<?php

include 'config.php';

$logourl="direwolf_ini.png";

$MYCALL = strtoupper($callsign);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="description" content="Direwolf dashboard - PU4RON">
<meta name="keywords" content="">
<meta name="author" content="IZ7BOJ">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Direwolf Dashboard</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #121212;
        color: #e0e0e0;
    }
    .container {
        text-align: center;
        padding: 60px;
    }
    h1 {
        font-size: 2.5rem;
        color: #ff9800;
    }
    h2 {
        font-size: 1.8rem;
        color: #03a9f4;
        margin-bottom: 30px;
    }
    form {
        display: inline-block;
        background: #1e1e1e;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
    }
    select, input[type="submit"] {
        font-size: 1rem;
        padding: 10px;
        margin-top: 20px;
        border: none;
        border-radius: 5px;
    }
    select {
        background: #292929;
        color: #e0e0e0;
    }
    input[type="submit"] {
        background: #808080;
        color: #121212;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background: #A9A9A9;
    }
    footer {
        margin-top: 50px;
        font-size: 0.9rem;
        color: #757575;
    }
</style>
</head>
<body>


<div class="container">
      <center><img src="<?php echo $logourl ?>" ></center>

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
        <label for="interface">Interface:</label>
        <select name="if" id="interface">
        <?php
        $i=0;
        for ($i=0;$i<=sizeof($interfaces)-1;$i++) {
        ?>
            <option value="<?php echo $interfaces[$i] ?>"><?php echo $interfaces[$i]." - ".$intdesc[$i] ?></option>
        <?php
        }
        ?>
        </select>
        <br>
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

    <footer>
        Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ.
    </footer>
</div>
</body>
</html>
