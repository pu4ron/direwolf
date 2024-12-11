<?php

include 'config.php';

$logDir = $logpath;

$searchCall = isset($_GET['getcall']) ? strtoupper(substr(trim($_GET['getcall']), 0, 10)) : ''; 
$results = [];

function convertZuluToBRTime($zuluTime) {
    $datetime = new DateTime($zuluTime, new DateTimeZone('UTC')); 
    $datetime->setTimezone(new DateTimeZone('America/Sao_Paulo'));
    return $datetime->format('H:i:s'); 
}

if (!empty($searchCall)) {
    $logFile = $logDir . '/' . date('Y-m-d') . '.log';

    if (file_exists($logFile)) {
        $logLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($logLines as $line) {
            $fields = str_getcsv($line, ','); 
            if (isset($fields[8]) && $fields[8] === $searchCall) {
                $results[] = $fields;
            }
        }
    } else {
        $error = "Arquivo de log n&atilde;o encontrado: $logFile";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de indicativo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #121212;
            color: #e0e0e0;
        }
        h1 {
            text-align: center;
            //color: #bb86fc;
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }





        .form-container label {
            font-family: 'Roboto', sans-serif;
            font-size: 1.2em;
            font-weight: 500;
            color: #e0e0e0;
        }




        .form-container input[type="text"] {
            padding: 8px;
            width: 200px;
            background-color: #1f1f1f;
            color: #e0e0e0;
            border: 1px solid #5a5a5a;
            border-radius: 5px;
        }


        .form-container input[type="submit"] {
            padding: 2px 10px;
            background-color: #d3d3d3;
            color: #121212;
            border: none;
            cursor: pointer;
            border-radius: 9px;
            font-family: 'Roboto', sans-serif;
            font-size: 1.2em;
            font-weight: 500;
        }
        .form-container input[type="submit"]:hover {
            background-color: #bfbfbf;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            white-space: nowrap;
        }
        table th {
            background-color: #0000ff;
            color: #ffffff;
        }
        table tr:nth-child(even) {
            background-color: #1e1e1e;
        }
        table tr:hover {
            background-color: #333;
        }
        .error {
            color: #cf6679;
            text-align: center;
            font-size: 1.2em;
        }
        .no-results {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 300px;
            height: auto;
        }
        .toggle-time-container {
            text-align: center;
            margin-top: 20px;
        }
        .toggle-time-container button {
            padding: 6px 12px;
            font-size: 14px;
            background-color: #bb86fc;
            color: #121212;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .toggle-time-container button:hover {
            background-color: #9b59b6;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="direwolf_ini.png" alt="Logo do Dashboard">
    </div>


    <div class="form-container">
        <form method="get">

        </form>
    </div>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Canal</th>
                    <th>Data</th>
                    <th class="time-cell" id="toggle-time-header" style="cursor: pointer;">Hora</th>
                    <th>Fonte</th>
                    <th>Nome</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Velocidade</th>
                    <th>Dire&ccedil;&atilde;o</th>
                    <th>Altitude</th>
                    <th>Frequ&ecirc;ncia</th>
                    <th>DUP</th>
                    <th>SubTom</th>
                    <th>Coment&aacute;rio/MSG</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $fields): 
                    $fields = array_pad($fields, 22, ''); 

                    $fullDate = $fields[2] ?? ''; 
                    $date = substr($fullDate, 0, strpos($fullDate, 'T'));
                    $time = substr($fullDate, strpos($fullDate, 'T') + 1);
                    $formattedDate = substr($date ? date('d-m-y', strtotime($date)) : '', 0, 8); 

                    $brTime = convertZuluToBRTime($fullDate); 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($fields[0] ?? '') ?></td>
                        <td><?= htmlspecialchars($formattedDate) ?></td>
                        <td class="time-cell" data-zulu="<?= htmlspecialchars($time) ?>" data-br="<?= htmlspecialchars($brTime) ?>">
                            UTC: <?= htmlspecialchars($time ?: 'Indefinido') ?></td>
                        <td><?= htmlspecialchars($fields[3] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[8] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[10] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[11] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[12] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[13] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[14] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[15] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[16] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[17] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[21] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results"><?= $searchCall ? "Nenhum resultado encontrado para '$searchCall'." : "Insira um indicativo para pesquisa." ?></p>
    <?php endif; ?>

    <br><br><br><br>
    <center><a><b>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</b></a></center>

    <script>
        document.getElementById('toggle-time-header')?.addEventListener('click', function() {
            const timeCells = document.querySelectorAll('.time-cell');
            timeCells.forEach(cell => {
                const isZulu = cell.textContent.startsWith('UTC:');
                const zuluTime = cell.dataset.zulu;
                const brTime = cell.dataset.br;

                if (zuluTime && brTime) {
                    if (isZulu) {
                        cell.textContent = 'BR: ' + brTime;
                    } else {
                        cell.textContent = 'UTC: ' + zuluTime;
                    }
                }
            });
        });
    </script>
</body>
</html>
