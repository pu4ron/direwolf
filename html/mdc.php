<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

$logDir = $logpath;
$linesToShow = 1;

function getLatestLogFile($logDir) {
    $files = glob("$logDir/*.log");
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    return $files[0] ?? null;
}

$logFile = getLatestLogFile($logDir);
$highlightedName = null;

function tailFile($file, $lines) {
    $data = '';
    if (!$file) return $data;

    $fp = fopen($file, "r");
    fseek($fp, -1, SEEK_END);
    $pos = ftell($fp);
    $buffer = '';

    while ($lines > 0 && $pos > 0) {
        $pos--;
        fseek($fp, $pos, SEEK_SET);
        $char = fgetc($fp);

        if ($char === "\n" && strlen($buffer) > 0) {
            $processedLine = "<div class='log-line'>" . addDotToStart(colorizeLogLine(filterLogLine($buffer))) . "</div>";
            $data = $processedLine . "\n" . $data;
            $buffer = '';
            $lines--;
        } else {
            $buffer = $char . $buffer;
        }
    }

    if (strlen($buffer) > 0) {
        $data = "<div class='log-line'>" . addDotToStart(colorizeLogLine(filterLogLine($buffer))) . "</div>\n" . $data;
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
        $parts[2] = "<span class='datetime'>$formattedDateTime</span>";
        $filteredLine = implode(': ', $parts);
    } else {
        $filteredLine = $line;
    }

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
    global $highlightedName;

    if (preg_match('/\b(PY[1-9]\w*-?\d*|PU[1-9]\w*-?\d*)\b/', $line, $match)) {
        if (!$highlightedName) {
            $highlightedName = $match[0];
        }
        $line = preg_replace('/\b(PY4\w*-?\d*|PU4\w*-?\d*)\b/', '<span class="color dark">$1</span>', $line, 1);
    }

    $line = preg_replace('/\b(apps:.*?)$/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(DireWolf:.*?)$/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(Kantronics.*?)$/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(APRSdroid.*?)$/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(Bravo Tracker.*?)$/i', '<span class="color destaque">$1</span>', $line);
    $line = preg_replace('/\b(MMDVM.*?)$/i', '<span class="color green">$1</span>', $line);

    return $line;
}

function addDotToStart($line) {
    return '<span class="dot">&#8226;</span> ' . $line;
}

if ($logFile && file_exists($logFile)) {
    echo "<div id='logOutput'>" . tailFile($logFile, $linesToShow) . "</div>";
    if ($highlightedName) {
        echo "<div id='highlightedName'>" . $highlightedName . "</div>";
    }
} else {
    echo "<div id='logOutput'>Nenhum arquivo de log encontrado!</div>";
}
?>

<audio id="beepSound" src="aprs.mp3" preload="auto"></audio>


<div id="beepToggle">
    <img id="beepIcon" src="on.png" alt="Beep On" onclick="toggleBeep()" />
</div>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log audio Direwolf</title>

<style>
body {
    background: 
        linear-gradient(rgba(255, 12, 25, 0.9), rgba(0, 0, 0, 2.5)),
        url('logMG.jpg') no-repeat center center fixed;
    background-size: cover;
    padding: 25px;
    //padding: 20px;
    font-family: Arial, sans-serif;
         //  color: white;
         //   margin: 0;
         //   overflow-x: hidden;

}


        #logOutput {
            font-family: monospace;
            background-color: rgba(245, 245, 245, 0.8);
            border-radius: 10px;
            //overflow-y: hidden;
            padding: 15px;
            margin: 20px auto;
            max-width: 90%;
            position: relative;
           // box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            //border: 2px solid #333;
    border: 3px solid black; 
            color: #000;
            overflow-wrap: break-word;
        }

        .log-line {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            font-size: 1rem;
        }

        .dot {
            color: #DC143C;
            font-weight: bold;
        }

.color.indicativos { color: #DC143C; font-weight: bold; }
.color.destaque { color: #00008B; font-style: italic; }
.datetime { color: #888888; font-style: italic; }
.color.dark { color: #DC143C; font-weight: bold; }
.color.orange { color: orange; font-weight: bold; }
.color.green { color: green; font-weight: bold; }



#highlightedName {
    //font-size: 220px;
    //font-size: 10vw;
    font-size: 15vw;
    color: DarkOrange;
    font-weight: bold;
    text-align: center;
    margin: 47px auto;
    width: 100%;
    line-height: 1.2;
    -webkit-text-stroke: 3px black;
        box-shadow: 0 8px 16px rgba(255, 0, 0, 0.6);
    text-shadow: 
        2px 2px 0 #000, 
       -2px 2px 0 #000, 
        2px -2px 0 #000, 
       -2px -2px 0 #000;

}

#beepToggle {
    position: fixed;
    bottom: 50px;
    right: 10px;
    cursor: pointer;
    display: none;
}

#beepIcon {
    width: 50px;
    transition: opacity 0.3s ease;
}

       @media (max-width: 768px) {
            #logOutput {
                font-size: 0.9rem;
            }
            #highlightedName {
                font-size: 14vw;
            }
        }

       @media (max-width: 350px) {
            #logOutput {
                font-size: 0.8rem;
                padding: 10px;
            }
            #highlightedName {
                font-size: 18vw;
            }
            #beepIcon {
                width: 30px;
            }
        }

</style>

<script>
let beepEnabled = true;
let lastHighlightedName = "";
let hideTimeout;

function toggleBeep() {
    beepEnabled = !beepEnabled;
    const beepIcon = document.getElementById('beepIcon');
    beepIcon.src = beepEnabled ? 'on.png' : 'off.png';
}

function fetchLog() {
    fetch(window.location.href)
        .then(response => response.text())
        .then(data => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');

            const logOutput = doc.querySelector('#logOutput');
            const highlightedName = doc.querySelector('#highlightedName');

            if (logOutput) {
                document.getElementById('logOutput').innerHTML = logOutput.innerHTML;
            }
            
            const newHighlightedName = highlightedName ? highlightedName.innerHTML.trim() : '';
            
            if (newHighlightedName !== lastHighlightedName) {
                lastHighlightedName = newHighlightedName;
                document.getElementById('highlightedName').innerHTML = newHighlightedName;
                if (beepEnabled) {
                    document.getElementById('beepSound').play();
                }
            }
        })
        .catch(err => console.error("Erro ao buscar log:", err));
}
setInterval(fetchLog, 3000);

document.body.addEventListener('mousemove', () => {
    const beepToggle = document.getElementById('beepToggle');
    beepToggle.style.display = 'block';
    clearTimeout(hideTimeout);
    hideTimeout = setTimeout(() => {
        beepToggle.style.display = 'none';
    }, 15000);
});
</script>
