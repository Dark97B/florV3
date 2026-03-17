<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Produção</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            margin: 0;
            padding: 0;
        }

        .top-bar {
            background-color: #111;
            text-align: center;
            padding: 15px 0;
        }

        .logo-img {
            height: 60px;
            max-width: 100%;
            object-fit: contain;
            display: inline-block;
        }

        .container {
            background: white;
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #111;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #111;
            color: white;
        }

        button {
            padding: 8px 16px;
            background: #111;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        button:hover {
            background: #333;
        }

        .voltar-simples {
            display: inline-block;
            margin: 20px auto;
            text-align: center;
            padding: 8px 20px;
            background-color: #111;
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .voltar-simples:hover {
            background-color: #333;
        }

        form {
            text-align: center;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <img src="/florV3/public/assets/img/logo-flor-cortada.png" alt="Flor de Cheiro" class="logo-img">
</div>

<div class="container">
    <h2>Relatório de Produção</h2>

    <form method="GET" action="/florV3/public/index.php">
        <input type="hidden" name="rota" value="relatorio-operadores">
        <label>Data Inicial: </label>
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($dataInicio ?? '') ?>">
        &nbsp;&nbsp;
        <label>Data Final: </label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($dataFim ?? '') ?>">
        &nbsp;&nbsp;
        <button type="submit">Buscar</button>
    </form>

    <?php if (!empty($resultados)): ?>

<table border="1" cellpadding="8" cellspacing="0" style="margin:auto; text-align:center;">
    <tr>
        <th>Operador</th>
        <th>Produzidos</th>
        <th>Cancelados</th>
    </tr>

    <?php foreach ($resultados as $linha): ?>
        <tr>
            <td><?= htmlspecialchars($linha['responsavel']) ?></td>

            <!-- PRODUZIDOS -->
            <td>
                <?php if ((int)$linha['quantidade_produzido'] > 0): ?>
                    <a href="/florV3/public/index.php?rota=detalhes-operador&tipo=produzido&responsavel=<?= urlencode($linha['responsavel']) ?>&data_inicio=<?= htmlspecialchars($dataInicio ?? '') ?>&data_fim=<?= htmlspecialchars($dataFim ?? '') ?>">
                        <?= (int)$linha['quantidade_produzido'] ?>
                    </a>
                <?php else: ?>
                    0
                <?php endif; ?>
            </td>

            <!-- CANCELADOS -->
            <td>
                <?php if ((int)$linha['quantidade_cancelado'] > 0): ?>
                    <a href="/florV3/public/index.php?rota=detalhes-operador&tipo=cancelado&responsavel=<?= urlencode($linha['responsavel']) ?>&data_inicio=<?= htmlspecialchars($dataInicio ?? '') ?>&data_fim=<?= htmlspecialchars($dataFim ?? '') ?>">
                        <?= (int)$linha['quantidade_cancelado'] ?>
                    </a>
                <?php else: ?>
                    0
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>