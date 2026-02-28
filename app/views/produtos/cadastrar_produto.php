<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto - Flor de Cheiro</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .top-bar {
            width: 100%;
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
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #111;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: 500;
            color: #333;
        }

        input {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            width: 100%;
        }

        button {
            padding: 12px;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-enviar {
            background-color: #2ecc71;
            color: white;
        }

        .btn-enviar:hover {
            background-color: #27ae60;
        }

        .btn-voltar {
            margin-top: 15px;
            background-color: #555;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            padding: 10px;
            border-radius: 8px;
        }

        .btn-voltar:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <img src="/florV3/public/assets/img/logo-flor-cortada.png" alt="Flor de Cheiro" class="logo-img">
</div>

<div class="container">
    <h2>📦 Cadastrar Produto</h2>

    <div id="notificacao" style="display:none; background:#dff0d8; color:#3c763d; padding:10px; border-radius:8px; margin-bottom:15px; text-align:center;">
    Produto salvo com sucesso!
</div>


<form id="formProduto">

        <label>Nome do Produto:
            <input type="text" name="nome" required>
        </label>

        <!-- Campo de Grupo (comentado) -->
        <!--
        <label>Grupo do Produto:
            <select name="grupo_id" required style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; width: 100%;">
                <option value="">Selecione</option>
                <?php /*foreach ($grupos as $grupo): ?>
                    <option value="<?= $grupo['id'] ?>"><?= htmlspecialchars($grupo['nome']) ?></option>
                <?php endforeach;*/ ?>
            </select>
        </label>
        -->

        <label>Valor (R$):
            <input type="number" name="valor" step="0.01" required>
        </label>

        <label>Porcentagem (%):
            <input type="number" name="porcentagem" min="0" max="100" required>
        </label>

        <label>Código do Produto:
            <input type="text" name="codigo" maxlength="20" required>
        </label>

        <button type="submit" class="btn-enviar">Salvar Produto</button>
    </form>

    <a href="/florV3/public/index.php?rota=painel" class="btn-voltar">⬅ Voltar</a>
</div>


<script>
document.getElementById('formProduto').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('/florV3/public/index.php?rota=salvar-produto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // ← AGORA LENDO JSON
    .then(data => {

        const alerta = document.getElementById('notificacao');
        alerta.style.display = 'block';
        alerta.textContent = data.message;

        if (data.success) {
            alerta.style.background = '#dff0d8';
            alerta.style.color = '#3c763d';
            form.reset();
        } else {
            alerta.style.background = '#f2dede';
            alerta.style.color = '#a94442';
        }

        setTimeout(() => {
            alerta.style.display = 'none';
        }, 3000);
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar o produto.');
    });
});
</script>


</body>
</html>
