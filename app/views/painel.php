<?php
session_start();

use app\models\Permissao;
require_once __DIR__ . '/../models/Permissao.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/verifica_login.php';

$usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuarioTipo = strtolower($_SESSION['usuario_tipo'] ?? 'colaborador');

$permissaoModel = new Permissao();
$permissoes = $permissaoModel->listarTodas();

// Função para verificar permissão
function pode($chave) {
    $usuarioTipo = strtolower($_SESSION['usuario_tipo'] ?? 'colaborador');
    if ($usuarioTipo === 'admin') return true;

    $permissaoModel = new \app\models\Permissao();
    $permissoes = $permissaoModel->listarTodas();

    foreach ($permissoes as $p) {
        if ($p['pagina'] === $chave && $p['tipo_usuario'] === $usuarioTipo) {
            return true;
        }
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Flor de Cheiro - Painel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .topo { background: #111; color: white; padding: 10px 20px; position: relative; height: 70px; }
        .topo h1 { font-family: "Brush Script MT", cursive; font-size: 28px; margin: 0; position: absolute; left: 50%; transform: translate(-50%, -50%); top: 50%; }
        .menu-btn { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-size: 24px; background: none; border: none; color: white; cursor: pointer; }
        .menu-lateral { position: fixed; top: 0; left: -295px; width: 250px; height: 100%; background: #111; color: white; padding: 20px; transition: left 0.3s ease; z-index: 999; overflow-y: auto; display: flex; flex-direction: column; justify-content: flex-start; }
        .menu-lateral.ativo { left: 0; }
        .menu-lateral .fechar { float: right; cursor: pointer; font-size: 20px; }
        .menu-conteudo { margin-top: 20px; }
        .sair-fixado { margin-top: 20px; padding-left: 0; text-align: left; }
        .btn-sair { width: 100%; padding: 10px; background-color:rgb(53, 22, 22); color: white; border: none; border-radius: 10px; cursor: pointer; text-align: center; }

        .status-container { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin: 20px; }
        .coluna { width: 250px; flex: 0 0 auto; background: #eee; padding: 15px; border-radius: 8px; }
        .coluna h3 { text-align: center; margin-top: 0; }
        .pedido { background: white; border-radius: 6px; padding: 8px; margin-bottom: 10px; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .ver-toggle { text-align: center; margin-top: 5px; cursor: pointer; font-size: 12px; color: blue; }

        .coluna.pendente { background-color: #f28b82; }
        .coluna.producao { background-color: #fbbc04; }
        .coluna.pronto   { background-color: #a7cdfa; }

        .botoes { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
        .botao-acao { background: white; border-radius: 10px; padding: 15px; width: 180px; text-align: center; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .botao-acao h4 { margin: 0 0 10px; }
        .botao-acao button { background: green; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .oculto { display: none; }

        /* ===== TAG DO TIPO (opcional, mantive) ===== */
        .tag-tipo{
            display:inline-flex; align-items:center; gap:6px;
            font-weight:700; font-size:11px; line-height:1;
            padding:3px 8px; border-radius:999px; color:#000; white-space:nowrap;
            box-shadow:0 1px 0 rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.06);
            vertical-align:middle; margin-left:6px;
        }
        /* 
        .tag-entrega  { background:#2ecc71; }
        .tag-retirada { background:#e74c3c; }
        .tag-tipo::before{ content:""; width:7px; height:7px; border-radius:50%; background:rgba(255,255,255,.9); opacity:.9; }

        ===== NOVO: Pílula do CÓDIGO (#L2000) com cor por tipo ===== */
        .codigo-badge{
            display:inline-flex; align-items:center; justify-content:center;
            min-width:72px; padding:4px 10px; border-radius:999px;
            font-weight:700; font-size:12px; line-height:1; color:#fff; letter-spacing:.3px;
            box-shadow:0 1px 0 rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.06);
        }
        .codigo-entrega  { background:#2ecc71; } /* verde */
        .codigo-retirada { background:#e74c3c; } /* vermelho */

/* garante que o topo seja referência para posicionamento absoluto */
.topo { position: relative; }

/* mantém exatamente o comportamento original do botão */
.menu-btn {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 24px;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    z-index: 20; /* frente aos outros elementos */
}

/* chapéu aplicado sem quebrar layout; use o arquivo PNG que você colocou */
.menu-btn::after {
    content: "";
    position: absolute;
    width: 54px;                /* ajuste o tamanho se quiser maior/menor */
    height: 35px;
    background-image: url('');
    background-size: contain;
    background-repeat: no-repeat;
    top: -17px;                 /* sobe o chapéu para ficar "em cima" do ícone */
    left: 4px;                  /* alinhamento horizontal em relação aos traços */
    transform: rotate(-18deg);  /* inclina um pouco para ficar mais natural */
    pointer-events: none;       /* não atrapalha cliques no botão */
    z-index: 25;
}

        
    </style>
</head>
<body>

<div class="topo">
    <button class="menu-btn" onclick="abrirMenu()">☰</button>
    <img src="/florV3/public/assets/img/logo-flor-cortada.png" alt="Flor de Cheiro" style="height: 55px; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); object-fit: contain;">
</div>

<div class="menu-lateral" id="menuLateral">
    <span class="fechar" onclick="fecharMenu()">&times;</span>

    <div class="perfil">
        <span><br><br>  🙋🏻‍♂️ Olá <strong><?= htmlspecialchars($usuarioNome) ?></strong></span>
    </div>

    <div class="menu-conteudo">
        <br><br>

        <?php if (pode('agenda')): ?>
            <a href="/florV3/public/index.php?rota=agenda" style="color:white; text-decoration:none;">📆 Agenda</a><br><br>
        <?php endif; ?>
        <?php if (pode('cadastrar-produto')): ?>
            <a href="/florV3/public/index.php?rota=cadastrar-produto" style="color:white; text-decoration:none;">➕ Cadastrar Produto</a><br><br>
        <?php endif; ?>
        <?php if (pode('cadastrar-vendedor')): ?>
            <a href="/florV3/public/index.php?rota=cadastrar-vendedor" style="color:white; text-decoration:none;">➕ Cadastrar Vendedor</a><br><br>
        <?php endif; ?>
        <?php if (pode('cadastrar-operador')): ?>
            <a href="/florV3/public/index.php?rota=cadastrar-operador" style="color:white; text-decoration:none;">➕ Cadastrar Operador</a><br><br>
        <?php endif; ?>
        <?php if (pode('lista-vendedores')): ?>
            <a href="/florV3/public/index.php?rota=lista-vendedores" style="color:white; text-decoration:none;">📑 Lista de Vendedores</a><br><br>
        <?php endif; ?>
        <?php if (pode('lista-produtos')): ?>
            <a href="/florV3/public/index.php?rota=lista-produtos" style="color:white; text-decoration:none;">📑 Lista de Produtos</a><br><br>
        <?php endif; ?>
        <?php if (pode('lista-operadores')): ?>
            <a href="/florV3/public/index.php?rota=lista-operadores" style="color:white; text-decoration:none;">📑 Lista de Operadores</a><br><br>
        <?php endif; ?>
        <?php if (pode('historico')): ?>
            <a href="/florV3/public/index.php?rota=historico" style="color:white; text-decoration:none;">📜 Ver Histórico</a><br><br>
        <?php endif; ?>
        <?php if (pode('cancelados')): ?>
            <a href="/florV3/public/index.php?rota=cancelados" style="color:white; text-decoration:none;">❌ Pedidos Cancelados</a><br><br>
        <?php endif; ?>
        <?php if (pode('usuarios')): ?>
            <a href="/florV3/public/index.php?rota=usuarios" style="color:white; text-decoration:none;">👥 Gerenciar Usuários</a><br><br>
        <?php endif; ?>
        <?php if (pode('relatorio-operadores')): ?>
            <a href="/florV3/public/index.php?rota=relatorio-operadores" style="color:white; text-decoration:none;">📊 Relatório de Produção</a><br><br>
        <?php endif; ?>
        <?php if (pode('permissoes')): ?>
            <a href="/florV3/public/index.php?rota=permissoes" style="color:white; text-decoration:none;">🔒 Permissões</a><br><br>
        <?php endif; ?>
        <?php if ($usuarioTipo === 'admin'): ?>
            <a href="/florV3/public/index.php?rota=editar-numero-pedido" style="color:white; text-decoration:none;">🔧 Nº Pedido Padrão</a><br><br>
        <?php endif; ?>
    </div>

    <div class="sair-fixado">
        <button class="btn-sair" onclick="confirmarLogout()">🚪 Sair</button>
    </div>
</div>

<br><br><br>
<h2 style="text-align: center;">Status dos Pedidos</h2>

<div id="conteudo">
<div class="status-container">
    <?php
    $cores = ['Pendente' => 'pendente', 'Produção' => 'producao', 'Pronto' => 'pronto'];

    // filtra só de hoje e ordena
    $dataHoje = date('Y-m-d');
    foreach ($agrupados as $status => &$listaFiltrada) {
        $listaFiltrada = array_filter($listaFiltrada, function ($pedido) use ($dataHoje) {
            return isset($pedido['data_abertura']) && $pedido['data_abertura'] === $dataHoje;
        });
        usort($listaFiltrada, function($a, $b) {
            $da = ($a['data_abertura'] ?? '1970-01-01') . ' ' . ($a['hora'] ?? '00:00');
            $db = ($b['data_abertura'] ?? '1970-01-01') . ' ' . ($b['hora'] ?? '00:00');
            return strtotime($db) <=> strtotime($da);
        });
    }
    unset($listaFiltrada);

    // === NOVO: classe de cor para o código
    function classeCodigoPHP($tipoRaw) {
        $t = strtolower($tipoRaw ?? '');
        $t = preg_replace('/^p_/', '', $t);
        if (str_contains($t, 'retirada')) return 'codigo-retirada';
        if (str_contains($t, 'entrega'))  return 'codigo-entrega';
        return '';
    }

    // tag (mantida, opcional)
    function badgeTipoPHP($tipoRaw) {
        $t = strtolower($tipoRaw ?? '');
        $t = preg_replace('/^p_/', '', $t);
        if (str_contains($t, 'retirada')) return '<span class="tag-tipo tag-retirada">RETIRADA</span>';
        if (str_contains($t, 'entrega'))  return '<span class="tag-tipo tag-entrega">ENTREGA</span>';
        return '';
    }

    foreach ($agrupados as $status => $lista):
        $classe = $cores[$status] ?? '';
    ?>
    <div class="coluna <?= $classe ?>">
        <h3><?= htmlspecialchars($status) ?></h3>
        <?php foreach ($lista as $i => $pedido): ?>
            <?php
                $numero = htmlspecialchars($pedido['numero_pedido'] ?? '');
                $nome   = htmlspecialchars($pedido['remetente'] ?? $pedido['nome'] ?? '');
                $produto= htmlspecialchars($pedido['produto'] ?? '');
                $data   = isset($pedido['data_abertura']) ? date('d/m', strtotime($pedido['data_abertura'])) : '';
                $hora   = isset($pedido['hora']) ? substr($pedido['hora'], 0, 5) : '';
                $classeCod = classeCodigoPHP($pedido['tipo'] ?? '');
                $badge  = badgeTipoPHP($pedido['tipo'] ?? '');
            ?>
            <div class="pedido <?= $i >= 4 ? 'oculto' : '' ?>">
                <span class="codigo-badge <?= $classeCod ?>">#<?= $numero ?></span> <?= $badge ?><br>
                <?= $nome ?><br>
                <?= $produto ?>  <?= $data ?> às <?= $hora ?>
            </div>
        <?php endforeach; ?>
        <?php if (count($lista) > 4): ?>
            <div class="ver-toggle" onclick="togglePedidos(this)">⬇ Ver mais</div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="botoes">
    <?php if (pode('cadastrar-pedido')): ?>
        <div class="botao-acao">
            <h4>Cadastrar Pedido</h4>
            <a href="/florV3/public/index.php?rota=escolher-tipo"><button>Acessar</button></a>
        </div>
    <?php endif; ?>

    <?php if (pode('acompanhamento')): ?>
        <div class="botao-acao">
            <h4>Acompanhar Pedidos</h4>
            <a href="/florV3/public/index.php?rota=acompanhamento"><button>Acessar</button></a>
        </div>
    <?php endif; ?>

    <?php if (pode('acompanhamento-atendente')): ?>
        <div class="botao-acao">
            <h4>Acompanhamento do Atendente</h4>
            <a href="/florV3/public/index.php?rota=acompanhamento-atendente"><button>Acessar</button></a>
        </div>
    <?php endif; ?>

    <?php if (pode('retiradas')): ?>
        <div class="botao-acao">
            <h4>Entregues</h4>
            <a href="/florV3/public/index.php?rota=retiradas"><button>Acessar</button></a>
        </div>
    <?php endif; ?>
</div>
</div>

<script>
function togglePedidos(botao) {
    const coluna = botao.parentElement;
    const ocultos = coluna.querySelectorAll('.pedido.oculto');
    const mostrandoMais = botao.dataset.expandido === "true";

    if (mostrandoMais) {
        coluna.querySelectorAll('.pedido').forEach((pedido, i) => { if (i >= 4) pedido.classList.add('oculto'); });
        botao.textContent = '⬇ Ver mais';
        botao.dataset.expandido = "false";
    } else {
        ocultos.forEach(p => p.classList.remove('oculto'));
        botao.textContent = '⬆ Ver menos';
        botao.dataset.expandido = "true";
    }
}

function abrirMenu() { document.getElementById('menuLateral').classList.add('ativo'); }
function fecharMenu() { document.getElementById('menuLateral').classList.remove('ativo'); }
function confirmarLogout() {
    if (confirm("Deseja realmente sair do sistema?")) {
        window.location.href = "/florV3/public/index.php?rota=logout";
    }
}

// === NOVO: mesma lógica da cor do código para o auto-refresh
function normalizeTipo(t){ return (t || '').toString().toLowerCase().replace(/^p_/, ''); }
function classeCodigoJS(tipo){
    const t = normalizeTipo(tipo);
    if (t.includes('retirada')) return 'codigo-retirada';
    if (t.includes('entrega'))  return 'codigo-entrega';
    return '';
}
// (mantive sua tag opcional)
function badgeTipoHTML(tipo) {
    const t = normalizeTipo(tipo);
    if (t.includes('retirada')) return '<span class="tag-tipo tag-retirada">RETIRADA</span>';
    if (t.includes('entrega'))  return '<span class="tag-tipo tag-entrega">ENTREGA</span>';
    return '';
}
</script>

<script>
// AUTO-REFRESH
function carregarPedidosAutomaticamente() {
    fetch('/florV3/public/index.php?rota=pedidos-status-json')
        .then(res => res.json())
        .then(data => {
            const statusContainer = document.querySelector('.status-container');
            statusContainer.innerHTML = '';

            const cores = { 'Pendente': 'pendente', 'Produção': 'producao', 'Pronto': 'pronto' };

            ['Pendente', 'Produção', 'Pronto'].forEach(status => {
                const lista = Array.isArray(data[status]) ? data[status].slice() : [];

                lista.sort((a, b) => {
                    const aData = (a.data_abertura || '1970-01-01') + 'T' + (a.hora || '00:00');
                    const bData = (b.data_abertura || '1970-01-01') + 'T' + (b.hora || '00:00');
                    return new Date(bData) - new Date(aData);
                });

                let html = `<div class="coluna ${cores[status]}"><h3>${status}</h3>`;

                lista.forEach((pedido, index) => {
                    const oculto = index >= 4 ? 'oculto' : '';
                    const numero = pedido.numero_pedido ?? '';
                    const nome   = pedido.remetente || pedido.nome || '';
                    const produto= pedido.produto || '';
                    const dataFmt= formatarData(pedido.data_abertura);
                    const hora   = (pedido.hora || '').substring(0,5);
                    const badge  = badgeTipoHTML(pedido.tipo);
                    const classeCod = classeCodigoJS(pedido.tipo);

                    html += `
                        <div class="pedido ${oculto}">
                            <span class="codigo-badge ${classeCod}">#${numero}</span> ${badge}<br>
                            ${nome}<br>
                            ${produto}  ${dataFmt} às ${hora}
                        </div>
                    `;
                });

                if (lista.length > 4) {
                    html += `<div class="ver-toggle" onclick="togglePedidos(this)" data-expandido="false">⬇ Ver mais</div>`;
                }

                html += `</div>`;
                statusContainer.innerHTML += html;
            });
        })
        .catch(err => console.error('Erro ao carregar pedidos:', err));
}

function formatarData(dataStr) {
    if (!dataStr) return '';
    const partes = dataStr.split('-');
    if (partes.length < 3) return dataStr;
    return `${partes[2]}/${partes[1]}`;
}

setInterval(carregarPedidosAutomaticamente, 2000);
carregarPedidosAutomaticamente();
</script>

</body>
</html>
