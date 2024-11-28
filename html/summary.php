<?php

/*
<b>Pesquisa de estacao: <button onclick="window.open('frames.php')">OK</button><br><br>
*/
//header('Refresh:30');
include 'config.php';
include 'functions.php';

$MYCALL=strtoupper($callsign);

if(file_exists('custom.php')) include 'custom.php';

logexists();
  
session_start(); //start session
if(!isset($_SESSION['if'])) { //if interface not defined
   	header('Refresh: 0; url=chgif.php?chgif=1');
	die();
}

$if = $_SESSION['if'];

?>
<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="Description" content="Direwolf dashboard" />
      <meta name="Keywords" content="" />
      <meta name="Author" content="IZ7BOJ" />
      <!-- next style is to show arrows in sortable table's column headers to indicate that the table is sortable -->
      <style type="text/css">
         table.sortable th:not(.sorttable_sorted):not(.sorttable_sorted_reverse):not(.sorttable_nosort):after {
         content: " \25B4\25BE"
         }
         table, th, td {
         border: 1px solid black;
         border-collapse: collapse;
         }
      </style>
      
    <title><?php echo "Direwolf dashboard: $MYCALL";?></title>

      <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f0g0f0; //#f4f4f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
         }

         button {
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            //background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.9);
            margin: 10px;         
         }


         button:hover {
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
         }

         button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
         }

         a.button-link {
            display: inline-block;
            text-decoration: none;
            color: white;
         }


        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }

        th, td {
            border: 10px color: black; //solid #ddd;
            padding: 5px;
            text-align: center;
        }

        th {
            //background-color: #ffd700;
            //color: blue;
        }

        td {
            //background-color: #f9f9f9;
        }


  img {
            margin-top: 20px;
        }

        hr {
            margin: 20px 20;
            border: 0;
            height: 1px;
            background: #ddd;
        }


      </style>

    <script>

        setInterval(function() {
            location.reload();
        }, 30000);
    </script>


   </head>
   <body>
      <?php
         if(file_exists($logourl)){
         ?>
      <center><img src="<?php echo $logourl ?>" width="200px" height="200px" align="middle"></center>
      <br>
      <?php
         }
         ?>
      <center>
         <font size="20"></b></font> <font color="red" size="20"><b><?php echo "$MYCALL";?></b></font>
    <br>
    <br>

         <button onclick="window.open('https://aprs.com.br')">Aprs server</button> 

    <a href="frames.php" class="button-link">
        <button>Pesquisa</button>
    </a>

    <button onclick="window.open('live.php')">Log direwolf</button>
    <button onclick="window.open('mdc.php')">Log sound</button> 


         
    <br>
    <br>
    <br>
    <br>
    <br>



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
        
      <br>
      <table style="text-align: left; height: 116px; width: 600px;" border="1" cellpadding="2" cellspacing="2">
         <tbody>
            <tr align="center">
                  <td bgcolor="#ffd700" style="width: 600px;" colspan="2" rowspan="1"><span
                  style="color: red; font-weight: bold;">DISPOSITIVO</span></td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>Vers&atilde;o do sistema operacional: </b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $sysver ?></td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>Kernel: </b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $kernelver ?></td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>Vers&atilde;o do Direwolf: </b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $direwolfver ?></td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>Tempo de atividade: </b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $uptime ?></td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>CPU temperature:</b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $cputemp ?> °C </td>
            </tr>
            <tr>
               <td bgcolor="silver" style="width: 200px;"><b>CPU frequency: </b></td>
               <td style="width: 400px; font-weight: bold;"><?php echo $cpufreq ?> MHz </td>
            </tr>
         </tbody>
      </table>
      <br><br>    <br>    <br>
      <hr>
    <hr>
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
               <th bgcolor="#ffd700"><b><font color="blue">Indicativo</font></b></th>
               <th bgcolor="#ffd700"><b><font color="blue">Qnt</font></b></th>
               <td bgcolor="#ffd700"><b><font color="blue">Mapa</font></b></td>
               <td bgcolor="#ffd700"><b><font color="blue">Frames</font></b></td>
               <th bgcolor="#ffd700"><b><font color="blue">Est&aacute;tico/Movimento</font></b></th>
               <th bgcolor="#ffd700"><b><font color="blue">Rota</font></b></th>
               <th bgcolor="#ffd700"><b><font color="blue">Data/Hora</font></b></th>
               <th bgcolor="#ffd700"><b><font color="blue">Dist&acirc;ncia</font></b></th>
               <th bgcolor="#ffd700"><b><font color="blue">Local</font></b></th>
            </tr>
            <?php
               foreach($receivedstations as $c=>$nm)
               {
               ?>
            <tr>
               <td bgcolor="silver"><b><?php echo $c ?></b></td>
               <td align="center"><?php echo $nm[0] ?></td>
               <td><?php echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'">aprs.fi</a>'?></td>
               <td><?php echo '<a target="_blank" href="frames.php?getcall='.$c.'">frames da esta&ccedil;&atilde;o</a>' ?></td>
               <td align="center">
                  <?php
                     if (in_array($c, $staticstations)) echo '<font color="purple">EST&Aacute;TICO</font>';
                     elseif (in_array($c, $movingstations)) echo '<font color="orange">EM MOVIMENTO</font>';
                     else echo "OTHER";
                     ?>
               </td>
               <td align="center">
                  <?php
                     if ((in_array($c, $directstations))&&(in_array($c, $viastations))) echo '<font color="BLUE">DIGI~DIRETO</font>';
                     elseif (in_array($c, $directstations)) echo '<font color="RED">PONTO-A-PONTO</font>';
                     else if (in_array($c, $viastations)) echo '<font color="GREEN">DIGI</font>';
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
      <br>
      <hr>
    <hr>
      <br>
      <center><a>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</center>
      <br>
   </body>
</html>


