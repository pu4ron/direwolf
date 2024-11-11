<?php

include 'config.php';

$logDir = $logpath;
$linesToShow = 18;

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

    //$line = preg_replace('/\b(PY4BN(-\d*)?)\b/', '<span class="color blue">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PY4IG(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PU4RON(-\d*)?)\b/', '<span class="color blue">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PY4RLA(-\d*)?)\b/', '<span class="color destaque1">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PY4ARR(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PY4AC(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);
    //$line = preg_replace('/\b(PY4AW(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);

    $line = preg_replace('/\b(PY4.*(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);
    $line = preg_replace('/\b(PU4.*(-\d*)?)\b/', '<span class="color indicativos">$1</span>', $line, 1);
    $line = preg_replace('/\b(PY4BN(-\d*)?)\b/', '<span class="color blue">$1</span>', $line, 1);
    $line = preg_replace('/\b(PU4RON(-\d*)?)\b/', '<span class="color blue">$1</span>', $line, 1);
    $line = preg_replace('/\b(PY4RLA(-\d*)?)\b/', '<span class="color blue">$1</span>', $line, 1);


    $line = preg_replace('/\b(apps:.*?)(\s|$)/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(DireWolf:.*)(\s|$)/i', '<span class="color destaque">$1</span>', $line);

    $line = preg_replace('/:([^:]*)$/', ',<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/,([^,]*)$/', ',<span class="color destaque">$1</span>', $line);

    return $line;
}

function addDotToStart($line) {
    return '<strong>.</strong> ' . $line;
}

if ($logFile && file_exists($logFile)) {
    echo "<pre id='logOutput'>" . tailFile($logFile, $linesToShow) . "</pre>";
} else {
    echo "<pre id='logOutput'>Nenhum arquivo de log encontrado.</pre>";
}
?>

<style>
.color.indicativos { color: #DC143C;}
//.color.indicativos { color: #DC143C; font-weight: bolder;}
.color.destaque { color: #00008B;}
.color.destaque1 { color: #b7410e; font-weight: bolder;}
.color.destaque2 { color: #006400; font-weight: bolder;}
.color.blue { color: blue; font-weight: bolder;}

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
