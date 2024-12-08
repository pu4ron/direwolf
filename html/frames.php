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
        $error = "Arquivo de log não encontrado: $logFile";
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
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container input[type="text"] {
            padding: 8px;
            width: 200px;
        }
        .form-container input[type="submit"] {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .toggle-time-container {
            text-align: center;
            margin: 15px auto;
        }
        .toggle-time-container button {
            padding: 6px 12px;
            font-size: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .toggle-time-container button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            white-space: nowrap;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .error {
            color: red;
            text-align: center;
            font-size: 1.2em;
        }
        .no-results {
            text-align: center;
            font-size: 1.2em;
            color: #666;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 300px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="direwolf.png" alt="Logo do Dashboard">
    </div>

    <h1>Pesquisa de indicativo</h1>
    <div class="form-container">
        <form method="get">
            <label for="getcall">Indicativo:</label>
            <input type="text" name="getcall" id="getcall" maxlength="10" value="<?= htmlspecialchars($searchCall) ?>">
            <input type="submit" value="Pesquisar">
        </form>
<br>
    </div>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (!empty($results)): ?>
        <div class="toggle-time-container">
            <button id="toggle-time">Fuso hor&aacute;rio</button>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Canal</th>
                    <th>Data</th>
                    <th>Hora</th>
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
                        <td class="time-cell" data-zulu="<?= htmlspecialchars($time) ?>" data-br="<?= htmlspecialchars($brTime) ?>">UTC: <?= htmlspecialchars($time) ?></td>
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
    <center><a><b>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</center>

    <script>
        document.getElementById('toggle-time')?.addEventListener('click', function() {
            const timeCells = document.querySelectorAll('.time-cell');
            timeCells.forEach(cell => {
                const isZulu = cell.textContent.startsWith('UTC:');
                if (isZulu) {
                    cell.textContent = 'BR: ' + cell.dataset.br;
                } else {
                    cell.textContent = 'UTC: ' + cell.dataset.zulu;
                }
            });
        });
    </script>
</body>
</html>
