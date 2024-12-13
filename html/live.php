<?php

include 'config.php';
include 'cores.php';

$logourl="direwolf_ini.png";

$logDir = $logpath;
$linesToShow = 6;

function getLatestLogFile($logDir) {
    $files = glob("$logDir/*.log");
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    return $files[0] ?? null;
}

$logFile = getLatestLogFile($logDir);

function tailFile($file, $lines) {
    $data = '';
    $fp = fopen($file, "r");
    fseek($fp, -1, SEEK_END);
    $pos = ftell($fp);
    $buffer = '';

    while ($lines > 0 && $pos > 0) {
        $pos--;
        fseek($fp, $pos, SEEK_SET);
        $char = fgetc($fp);

        if ($char === "\n" && strlen($buffer) > 0) {
            $processedLine = addDotToStart(colorizeLogLine(filterLogLine($buffer)));
            $data = $processedLine . "\n\n" . $data;
            $buffer = '';
            $lines--;
        } else {
            $buffer = $char . $buffer;
        }
    }

    if (strlen($buffer) > 0) {
        $data = addDotToStart(colorizeLogLine(filterLogLine($buffer))) . "\n\n" . $data;
    }

    fclose($fp);
    return $data;
}

function filterLogLine($line) {
    $parts = explode(',', $line);
    if (count($parts) > 2) {
        unset($parts[0], $parts[1]);
        $dateTime = $parts[2];
        $formattedDateTime = formatDateTime($dateTime);
        $parts[2] = $formattedDateTime;
        $filteredLine = implode(':', $parts); 
    } else {
        $filteredLine = $line;
    }

    $filteredLine = preg_replace('/:+/', ':', $filteredLine);  
    return trim($filteredLine);
}

function formatDateTime($dateTime) {
    try {
        $date = new DateTime($dateTime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        return $date->format('H:i:s');
    } catch (Exception $e) {
        return $dateTime;
    }
}

function colorizeLogLine($line) {

    $line = preg_replace('/\b(P[A-Z]\d+[A-Z]+(-\d*)?)\b/', '<span class="color laranja">$1</span>', $line, 1);
    $line = preg_replace('/\b(apps:.*?)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(DireWolf:.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/:([^:]*)$/', ',<span class="color laranja">$1</span>', $line);
    $line = preg_replace('/,([^,]*)$/', ',<span class="color laranja">$1</span>', $line);
    $line = preg_replace('/\b(SARTRACK:meshbrasil.com.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(SARTRACK:SARTrack.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(SQ8L.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(PY5BK Bravo Tracker in Brazil.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(APRSdroid Android App.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(Bat:.*)(\s|$)/i', '<span class="color azul">$1</span>', $line);
    $line = preg_replace('/\b(Xastir:.*)(\s|$)/i', '<span class="color verde">$1</span>', $line);
    $line = preg_replace('/\b(Yaesu FTM-100D.*)(\s|$)/i', '<span class="color marrom-claro">$1</span>', $line);
    $line = preg_replace('/\b(Yaesu FT5D:In Service.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(Kantronics KPC-3.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(Generic:.*)(\s|$)/i', '<span class="color vermelho">$1</span>', $line);
    $line = preg_replace('/\b(KA2DDO Yet another APRS.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);
    $line = preg_replace('/\b(openSPOT3 by HA2NON.*)(\s|$)/i', '<span class="color vinho-claro">$1</span>', $line);

    return $line;
}

function addDotToStart($line) {
    return '<strong style="box-shadow: 0 0px 9px rgba(0, 0, 0, 0.9); font-size: 20px; padding: 2px; color: Gray;">-></strong> ' . $line;
}

if ($logFile && file_exists($logFile)) {
    echo "<pre id='logOutput'>" . tailFile($logFile, $linesToShow) . "</pre>";
} else {
    echo "<pre id='logOutput'>Nenhum arquivo de log encontrado.</pre>";
}
?>

<style>

body {
    background-color: #1E1E1E; 
    color: #90EE90; 
    font-family: 'Arial', sans-serif; 
    font-size: 24px;
    line-height: 2.5; 
    margin: 0;
    padding: 0;


}

#logOutput {
    padding: 20px;
    margin: 0; 
    position: absolute;
    top: 0; 
    left: 0; 
    width: 100%; 
    max-width: 100%; 
    border: 0px solid #333;
    background-color: #1E1E1E; 
    font-size: 15px;
}

</style>

<script>
let currentLogFile = "<?php echo $logFile; ?>";

function fetchLog() {
    fetch("<?php echo basename(__FILE__); ?>")
        .then(response => response.text())
        .then(data => {
            document.getElementById('logOutput').innerHTML = data;
        });
}

function checkLatestLogFile() {
    fetch("<?php echo basename(__FILE__); ?>?checkNewFile=1")
        .then(response => response.text())
        .then(data => {
            if (data !== currentLogFile) {
                currentLogFile = data;
                fetchLog(); 
            }
        });
}

setInterval(function() {
    checkLatestLogFile();
    fetchLog();
}, 3000);
</script>
