<?php

//header('Refresh:30');
include 'config.php';
include 'functions.php';

$logourl="direwolf_ini.png";

$MYCALL = strtoupper($callsign);

if (file_exists('custom.php')) include 'custom.php';

logexists();
  
session_start(); //start session
if (!isset($_SESSION['if'])) { 
    header('Refresh: 0; url=chgif.php?chgif=1');
    die();
}

$if = $_SESSION['if'];

?>
<!DOCTYPE html>
<html lang="pt-br">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="Description" content="Direwolf dashboard" />
      <meta name="Keywords" content="" />
      <meta name="Author" content="IZ7BOJ" />
      <style type="text/css">
         body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212; /* Dark background */
            color: #e0e0e0; /* Light text */
            text-align: center;
            margin: 0;
            padding: 20px;
         }

         h2 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 20px;
         }

         button {
            background-color: #1e88e5;
            border: none;
            color: white;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 18px;
            margin: 10px 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
         }

         button:hover {
            background-color: #1565c0;
         }

         table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #212121; /* Dark table background */
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
         }

         th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #444;
         }

         th {
            background-color: #1e88e5; /* Blue background for header */
            color: white;
         }

         tr:hover {
            background-color: #333;
         }

         select {
            padding: 8px 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #333;
            color: white;
         }

         hr {
            margin: 40px 20px;
            border: 0;
            border-top: 1px solid #444;
         }

         .container {
            max-width: 1200px;
            margin: 0 auto;
         }

         .logo img {
            max-width: 200px;
            height: auto;
         }

      </style>

      <script>
         setInterval(function() {
            location.reload();
         }, 30000);
      </script>

      <title><?php echo "Direwolf dashboard: $MYCALL";?></title>
   </head>
   <body>
      <div class="container">
<?php
         if(file_exists($logourl)){
         ?>
      <center><img src="<?php echo $logourl ?>" width="350px" height="250px" align="middle"></center>
      <br>
      <?php
         }
         ?>
      <center>
         <font size="20"></b></font> <font color="red" size="20"><b><?php echo ""; echo "$MYCALL";?></b></font>
    <br>
    <br>


         <a href="frames.php">
            <button>PESQUISA</button>
         </a>
         <button onclick="window.open('http://aprs.com.br:14501')">APRS</button>
         <button onclick="window.open('live.php')">LOG DIREWOLF</button>
         <button onclick="window.open('mdc.php')">RECEPTION SOUND</button>
         
         <br><br>

    <?php         // System parameters reading
         $sysver      = NULL;
         $kernelver   = NULL;
         $direwolfver = NULL;
         $cputemp     = NULL;
         $cpufreq     = NULL;
         $uptime      = NULL;
         
         $sysver = shell_exec ("cat /etc/os-release | grep PRETTY_NAME |cut -d '=' -f 2");
         $kernelver = shell_exec ("uname -r");
         $direwolfver = shell_exec ("direwolf --v | grep -m 1 'version' | cut -d ' ' -f 5");
         if (file_exists ("/sys/class/thermal/thermal_zone0/temp")) {
             exec("cat /sys/class/thermal/thermal_zone0/temp", $cputemp);
             $cputemp = $cputemp[0] / 1000;
         }
         if (file_exists ("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq")) {
         	exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq", $cpufreq);
         	$cpufreq = $cpufreq[0] / 1000;
         }
         $uptime = shell_exec('uptime -p');
		?>

         <table>
            <thead>
               <tr>
                  <th colspan="2">DISPOSITIVO</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td><b>Sistema operacional:</b></td>
                  <td><b><?php echo $sysver ?></b></td>
               </tr>
               <tr>
                  <td><b>Kernel:</b></td>
                  <td><b><?php echo $kernelver ?></b></td>
               </tr>
               <tr>
                  <td><b>Direwolf:</b></td>
                  <td><b><?php echo $direwolfver ?></b></td>
               </tr>
               <tr>
                  <td><b>Tempo de atividade:</b></td>
                  <td><b><?php echo $uptime ?></b></td>
               </tr>
               <tr>
                  <td><b>Temperatura da CPU:</b></td>
                  <td><b><?php echo $cputemp ?> &#176;C</b></td>
               </tr>
               <tr>
                  <td><b>Frequ&ecirc;ncia da CPU:</b></td>
                  <td><b><?php echo $cpufreq ?> MHz</b></td>
               </tr>
            </tbody>
         </table>

         <br><br>

	     <?php		
		 $time = 0; //start of the time from which to read data from log in Unix timestamp type
		 if(!isset($_GET['time']) or ($_GET['time'] == "")) { //if time range not specified
			$time = time() - 86400; //so take frames from last 1 hour			
		 }
		 elseif($_GET['time'] == "e") { //if whole log
			$time = 0;
		 }
		 else { //else if the time range is choosen
			$time = time() - ($_GET['time'] * 3600); //convert hours to seconds
		 }

		 $receivedstations = array();        
		 $staticstations = array();
		 $movingstations = array();
		 $otherstations = array();
		 $directstations = array(); //stations received directly
		 $viastations = array(); //stations received via digi
		 $lines = 0;
		 
		 $logfile = file($log); //read log file
		 $linesinlog = count($logfile);
		 while ($lines < $linesinlog) { //read line by line
			$line = $logfile[$lines];
			stationparse($line); // build received stations table
			$lines++;
		}
		 
                 echo "<br><br><b>Canal ".$if.": ".count($receivedstations)." esta&ccedil;&otilde;es recebidas.</b><br><br>";

		 uasort($receivedstations, 'cmp');
		 echo "<b>Quantidade de quadros no log: <b></b>".$linesinlog;

         ?>
	  <br><br>
      <form action="summary.php" method="GET">

      <b>Eventos e a&ccedil;&otilde;es:
      <select name="time">
         <option value="1" <?php if(isset($_GET['time'])&&($_GET['time'] == 1)) echo 'selected="selected"'?>>1 hora</option>
         <option value="2" <?php if(isset($_GET['time'])&&($_GET['time'] == 2)) echo 'selected="selected"'?>>2 horas</option>
         <option value="4" <?php if(isset($_GET['time'])&&($_GET['time'] == 4)) echo 'selected="selected"'?>>4 horas</option>
         <option value="6" <?php if(isset($_GET['time'])&&($_GET['time'] == 6)) echo 'selected="selected"'?>>6 horas</option>
         <option value="12" <?php if(isset($_GET['time'])&&($_GET['time'] == 12)) echo 'selected="selected"'?>>12 horas</option>
         <option value="24" <?php if(isset($_GET['time'])&&($_GET['time'] == 24)) echo 'selected="selected"'?>>1 dia</option>
         <option value="e" <?php if(isset($_GET['time'])&&($_GET['time'] == 'e')) echo 'selected="selected"'?>>Todos</option>
      </select>
      <input type="submit" value="Atualizar">
      </form>
	  <br>
	  <?php
          /******
	  echo "<br><br><b>".count($receivedstations)." Esta&ccedil;&otilde;es recebidas na interface de r&aacute;dio ".$if." </b><br><br>";
          ******/
	  ?>
	  <br>
	  <script src="sorttable.js"></script>
      <table style="text-align: left; height: 116px; width: 1200px;" border="1" class="sortable" id="table">
         <tbody>
               <tr>
                  <th><b>Indicativo</b></th>
                  <th><b>Qnt</b></th>
                  <th><b>Mapa</b></th>
                  <th><b>Frames</b></th>
                  <th><b>Est&aacute;tico/Movimento</b></th>
                  <th><b>Rota</b></th>
                  <th><b>Data/Hora</b></th>
                  <th><b>Dist&acirc;ncia</b></th>
                  <th><b>Local</b></th>
               </tr>
            </thead>
            <tbody>
             <?php
               foreach($receivedstations as $c=>$nm)
               {
               ?>
            <tr>
               <td bgcolor="#2F4F4F"><b><?php echo $c ?></b></td>
               <td align="center"><?php echo $nm[0] ?></td>
               <td><?php echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'" style="color: DarkTurquoise;">aprs.fi</a>' ?></td>
               <td><?php echo '<a target="_blank" href="frames.php?getcall='.$c.'" style="color: DarkTurquoise;">Frames da esta&ccedil;&atilde;o</a>' ?></td>
               <td align="center">
                  <?php
                     if (in_array($c, $staticstations)) echo '<font color="RoyalBlue">EST&Aacute;TICO</font>';
                     elseif (in_array($c, $movingstations)) echo '<font color="PaleVioletRed">EM MOVIMENTO</font>';
                     else echo "OTHER";
                     ?>
               </td>
               <td align="center">
                  <?php
                     if ((in_array($c, $directstations))&&(in_array($c, $viastations))) echo '<font color="SandyBrown">DIGI~DIRETO</font>';
                     elseif (in_array($c, $directstations)) echo '<font color="SandyBrown">PONTO-A-PONTO</font>';
                     else if (in_array($c, $viastations)) echo '<font color="RoyalBlue">DIGI</font>';
                         ?>
               </td>
               <td align="center">
                  <?php
                     echo(date('m/d/Y H:i:s', $nm[1]))
                     ?>
               </td>
               <td align="center">
                  <?php
                   if (isset($nm[2])) :
		       if ($miles == 0):
                                echo $nm[2]." Km";
                        else:
                                echo $nm[2]*0.6214." miles";
                        endif;
                   else:
                        echo "N/A";
                   endif;
                     ?>
               </td>
               <td align="center">
                  <?php
                    if (isset($nm[3])):
                        echo $nm[3]." °";
                    else:
                        echo "NA";
                    endif;
                     ?>
               </td>
            </tr>
            <?php
                   }
               ?>
            </tbody>
         </table>

         <br><hr><br>
         <center><a>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</center>
      </div>
   </body>
</html>
