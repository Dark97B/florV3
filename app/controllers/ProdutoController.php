<?php
require_once __DIR__ . '/../models/Produto.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Grupo.php';

class ProdutoController {

    public function cadastrarProduto() {
        require __DIR__ . '/../views/produtos/cadastrar_produto.php';
    }

    public function salvarProduto()
    {
        header('Content-Type: application/json');

        $nome = $_POST['nome'] ?? '';
        $valor = $_POST['valor'] ?? '';
        $codigo = trim($_POST['codigo'] ?? '');
        $porcentagem = $_POST['porcentagem'] ?? null;

        if (!$nome || $valor === '' || !$codigo) {
            echo json_encode([
                'success' => false,
                'message' => 'Preencha todos os campos obrigatórios.'
            ]);
            exit;
        }

        try {
            $db = Database::conectar();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $produtoModel = new Produto($db);
            $produtoModel->salvar($nome, $valor, $codigo, $porcentagem);

            echo json_encode([
                'success' => true,
                'message' => 'Produto cadastrado com sucesso!'
            ]);

        } catch (PDOException $e) {

            if ($e->getCode() == 23505) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Já existe um produto com esse código.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro interno ao salvar produto.'
                ]);
            }
        }

        exit;
    }

    public function formularioCadastro() {
        $db = Database::conectar();
        $grupoModel = new Grupo($db);
        $grupos = $grupoModel->listarTodosAtivos();
        require __DIR__ . '/../views/produtos/cadastrar_produto.php';
    }

    public function listaProdutos() {
        $db = Database::conectar();
        $produtoModel = new Produto($db);
        $produtos = $produtoModel->listarTodosOrdenadoPorNome();
        require __DIR__ . '/../views/produtos/lista_produtos.php';
    }

    public function formularioCadastrarGrupo() {
        require __DIR__ . '/../views/produtos/cadastrar_grupo.php';
    }

    public function salvarGrupo() {
        $db = Database::conectar();
        $grupoModel = new Grupo($db);

        $nomeGrupo = $_POST['nome_grupo'] ?? '';

        if (!empty($nomeGrupo)) {
            $grupoModel->salvar($nomeGrupo);
            header("Location: /florV3/public/index.php?rota=cadastrar-grupo&sucesso=1");
            exit;
        } else {
            echo "Nome do grupo é obrigatório.";
        }
    }

    public function listarGrupos() {
        $db = Database::conectar();
        $grupoModel = new Grupo($db);
        $grupos = $grupoModel->listarTodos();
        require __DIR__ . '/../views/produtos/lista_grupos.php';
    }

    public function inativarGrupo() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::conectar();
            $grupoModel = new Grupo($db);
            $grupoModel->inativar($id);
        }
        header('Location: /florV3/public/index.php?rota=lista-grupos');
        exit;
    }

    public function excluirGrupo() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::conectar();
            $grupoModel = new Grupo($db);
            $grupoModel->excluir($id);
        }
        header('Location: /florV3/public/index.php?rota=lista-grupos');
        exit;
    }

    public function ativarGrupo() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $db = Database::conectar();
            $grupoModel = new Grupo($db);
            $grupoModel->ativar($id);
        }
        header('Location: /florV3/public/index.php?rota=lista-grupos');
        exit;
    }

    public function ativarProduto() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $produtoModel = new Produto(Database::conectar());
            $produtoModel->ativar($id);
        }
        header('Location: /florV3/public/index.php?rota=lista-produtos');
        exit;
    }

    public function inativarProduto() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $produtoModel = new Produto(Database::conectar());
            $produtoModel->inativar($id);
        }
        header('Location: /florV3/public/index.php?rota=lista-produtos');
        exit;
    }

    public function editarProduto() {
        $id = $_GET['id'] ?? null;
        $produtoModel = new Produto(Database::conectar());
        $produto = $produtoModel->buscarPorId($id);
        require __DIR__ . '/../views/produtos/editar_produto.php';
    }

    public function salvarEdicao() {
        $id          = $_POST['id'];
        $nome        = $_POST['nome'];
        $codigo      = $_POST['codigo'];
        $valor       = $_POST['valor'];
        $porcentagem = $_POST['porcentagem'] ?? null;

        $produtoModel = new Produto(Database::conectar());
        $produtoModel->atualizar($id, $nome, $codigo, $valor, $porcentagem);

        header('Location: /florV3/public/index.php?rota=lista-produtos');
        exit;
    }
}