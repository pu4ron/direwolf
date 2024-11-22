<?php

include 'config.php';

$logDir = $logpath;

$searchCall = isset($_GET['getcall']) ? strtoupper(trim($_GET['getcall'])) : '';
$results = [];

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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
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
            <input type="text" name="getcall" id="getcall" value="<?= htmlspecialchars($searchCall) ?>">
            <input type="submit" value="Pesquisar">
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
                    <th>Hora</th>
                    <th>Fonte</th>
                    <th>Nome</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Velocidade</th>
                    <th>Curso</th>
                    <th>Altitude</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $fields): ?>
                    <tr>
                        <td><?= htmlspecialchars($fields[0] ?? '') ?></td>
                        <td><?= htmlspecialchars(substr($fields[2], 0, strpos($fields[2], 'T'))) ?></td>
                        <td><?= htmlspecialchars(substr($fields[2], strpos($fields[2], 'T') + 1)) ?></td>
                        <td><?= htmlspecialchars($fields[3] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[8] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[10] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[11] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[12] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[13] ?? '') ?></td>
                        <td><?= htmlspecialchars($fields[14] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results"><?= $searchCall ? "Nenhum resultado encontrado para '$searchCall'." : "Insira um indicativo para pesquisa." ?></p>
    <?php endif; ?>

	<br><br><br><br><br><br><br><br><br>
	<center><a>Direwolf dashboard - Modificado por: Ronualdo PU4RON. Desenvolvido por Alfredo IZ7BOJ</center>

</body>
</html>
