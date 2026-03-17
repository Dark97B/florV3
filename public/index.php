<?php

date_default_timezone_set('America/Recife');

require_once __DIR__ . '/../app/controllers/PedidoController.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../app/controllers/ProdutoController.php';
require_once __DIR__ . '/../app/controllers/VendedorController.php';
require_once __DIR__ . '/../app/controllers/OperadorController.php';

$rota = $_GET['rota'] ?? 'painel';
$controller = new PedidoController();
$usuarioController = new UsuarioController();
$produtoController = new ProdutoController();
$vendedorController = new VendedorController();

switch ($rota) {

    // ---------- Pedidos ----------
    case 'painel':
        $controller->painelComPedidos();
        break;

    case 'escolher-tipo':
        require_once __DIR__ . '/../app/views/pedidos/escolher_tipo.php';
        break;

    case 'cadastrar-pedido':
        $controller->cadastrar();
        break;

    case 'salvar-pedido':
        $controller->salvar();
        break;

    case 'cadastrar-entrega':
        $controller->formEntrega();
        break;

    case 'cadastrar-retirada':
        $controller->formRetirada();
        break;

    case 'salvar-entrega':
        $controller->salvarEntrega();
        break;

    case 'salvar-retirada':
        $controller->salvarRetirada();
        break;

    case 'historico':
        $controller->historico();
        break;

    case 'detalhes':
        $id = $_GET['id'] ?? null;
        $tipo = $_GET['tipo'] ?? null;
        if ($id && $tipo) {
            $controller->detalhesPedido($id, $tipo);
        } else {
            echo "❌ Pedido não encontrado.";
        }
        break;

    case 'acompanhamento':
        $controller->acompanharPedidos();
        break;

    case 'acompanhamento-atendente':
        $controller->acompanhamentoAtendente();
        break;

    case 'atualizar-status':
        $controller->atualizarStatus();
        break;

    case 'retiradas':
        $controller->retiradas();
        break;

    case 'imprimir-pedido':
        $controller->imprimirPedido();
        break;

    case 'imprimir-ordem':
        $controller->imprimirOrdem();
        break;

    case 'imprimir-cupom-cliente':
        $controller->imprimirCupomCliente();
        break;

    case 'registrar-responsavel':
        $controller->registrarResponsavel();
        break;

    case 'detalhes-operador':
        $controller->detalhesOperador();
        break;

    case 'pedidos-status-json':
        $controller->retornarPedidosPorStatus();
        break;

    case 'buscar-pedidos-dia-json':
        $controller->buscarPedidosDoDiaJson();
        break;

    case 'buscar-pedidos-atendente-json':
        $controller->buscarPedidosAtendenteJson();
        break;

    case 'buscar-pedidos-produto':
        $controller->buscarPedidosPorProdutoAjax();
        break;

    case 'cancelados':
        $controller->cancelados();
        break;

    // ---------- Usuários ----------
    case 'usuarios':
        $usuarioController->listarUsuarios();
        break;

    case 'novo-usuario':
        $usuarioController->formularioCadastro();
        break;

    case 'salvar-usuario':
        $usuarioController->salvarCadastro();
        break;

    case 'login':
        $usuarioController->login();
        break;

    case 'autenticar':
        $usuarioController->autenticar();
        break;

    case 'logout':
        $usuarioController->logout();
        break;

    case 'atualizar-status-usuario':
        $usuarioController->atualizarStatusUsuario();
        break;

    // ---------- Vendedores ----------
    case 'cadastrar-vendedor':
        $vendedorController->cadastrar();
        break;

    case 'salvar-vendedor':
        $vendedorController->salvar();
        break;

    case 'lista-vendedores':
        $vendedorController->listar();
        break;

    case 'salvar-status-vendedores':
        $vendedorController->salvarStatusVendedores();
        break;

    case 'excluir-vendedor':
        $vendedorController->excluirVendedor();
        break;

    case 'ativar-vendedor':
        $vendedorController->ativar();
        break;

    case 'inativar-vendedor':
        $vendedorController->inativar();
        break;

    case 'atualizar-status-vendedor':
        $vendedorController->atualizarStatus();
        break;

    // ---------- Produtos ----------
    case 'cadastrar-produto':
        $produtoController->formularioCadastro();
        break;

    case 'salvar-produto':
        $produtoController->salvarProduto();
        break;

    case 'lista-produtos':
        $produtoController->listaProdutos();
        break;

    case 'editar-produto':
        $produtoController->editarProduto();
        break;

    case 'salvar-edicao-produto':
        $produtoController->salvarEdicao();
        break;

    case 'ativar-produto':
        $produtoController->ativarProduto();
        break;

    case 'inativar-produto':
        $produtoController->inativarProduto();
        break;

    case 'cadastrar-grupo':
        $produtoController->formularioCadastrarGrupo();
        break;

    case 'salvar-grupo':
        $produtoController->salvarGrupo();
        break;

    case 'lista-grupos':
        $produtoController->listarGrupos();
        break;

    case 'inativar-grupo':
        $produtoController->inativarGrupo();
        break;

    case 'excluir-grupo':
        $produtoController->excluirGrupo();
        break;

    case 'ativar-grupo':
        $produtoController->ativarGrupo();
        break;

    // ---------- Operadores ----------
    case 'cadastrar-operador':
        (new OperadorController())->cadastrarOperador();
        break;

    case 'salvar-operador':
        (new OperadorController())->salvarOperador();
        break;

    case 'lista-operadores':
        (new OperadorController())->listaOperadores();
        break;

    case 'relatorio-operadores':
        (new OperadorController())->relatorioOperadores();
        break;

    case 'editar-operador':
        $controller->editarOperador();
        break;

    case 'atualizar-operador':
        $controller->atualizarOperador();
        break;

    case 'excluir-operador':
        $controller->excluirOperador();
        break;

    // ---------- Outros ----------
    case 'acesso-negado':
        require_once __DIR__ . '/../app/views/usuarios/acesso_negado.php';
        break;

    case 'agenda':
        require_once __DIR__ . '/../app/views/agenda.php';
        break;

    case 'permissoes':
        require_once __DIR__ . '/../app/views/permissoes/permissoes.php';
        break;

    case 'editar-numero-pedido':
        require_once __DIR__ . '/../app/views/configuracoes/editar_numero_pedido.php';
        break;

    case 'notificacoes-futuras':
        require_once __DIR__ . '/../app/controllers/NotificacaoController.php';
        (new NotificacaoController())->listarPedidosFuturos();
        break;

    case 'marcar-notificacao-lida':
        require_once __DIR__ . '/../app/controllers/NotificacaoController.php';
        (new NotificacaoController())->marcarComoLido();
        break;


    case 'editar-usuario':
    $usuarioController->editarUsuario();
    break;

case 'salvar-edicao-usuario':
    $usuarioController->salvarEdicaoUsuario();
    break;



case 'detalhamento':
    require_once __DIR__ . '/../app/controllers/RelatorioController.php';
    $controller = new RelatorioController();
    $controller->detalhamento();
break;


case 'detalhes-operador':
    require_once __DIR__ . '/../app/controllers/OperadorController.php';
    $controller = new OperadorController();
    $controller->detalhesOperador();
    break;

    
    // ---------- Default ----------
    default:
        echo "Página não encontrada.";
        break;
}
