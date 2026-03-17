<?php
require_once __DIR__ . '/../models/Operador.php';
require_once __DIR__ . '/../../config/database.php';

class OperadorController {

    public function cadastrarOperador() {
        require __DIR__ . '/../views/operadores/cadastrar_operador.php';
    }

    public function salvarOperador() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');

            if ($nome) {
                $operador = new Operador();
                $operador->criar($nome);
            }

            header('Location: /florV3/public/index.php?rota=lista-operadores');
            exit;
        }
    }

    public function listaOperadores() {
        $operador = new Operador();
        $operadores = $operador->listarTodos();
        require __DIR__ . '/../views/operadores/lista_operadores.php';
    }

    public function relatorioOperadores() {
        require_once __DIR__ . '/../models/ResponsavelProducao.php';
        $responsavelModel = new ResponsavelProducao();

        $dataInicio = $_GET['data_inicio'] ?? '';
        $dataFim = $_GET['data_fim'] ?? '';
        $resultados = [];

        if ($dataInicio && $dataFim) {
            $resultados = $responsavelModel->listarPorPeriodo($dataInicio, $dataFim);
        }

        require __DIR__ . '/../views/operadores/relatorio_operadores.php';
    }


    
    public function detalhesOperador() {
        require_once __DIR__ . '/../models/ResponsavelProducao.php';
        $model = new ResponsavelProducao();

        $responsavel = $_GET['responsavel'] ?? '';
        $tipo = $_GET['tipo'] ?? '';
        $inicio = $_GET['data_inicio'] ?? '';
        $fim = $_GET['data_fim'] ?? '';

        $pedidos = $model->listarDetalhes($responsavel, $tipo, $inicio, $fim);

        require __DIR__ . '/../views/operadores/detalhes_operador.php';
    }

public function detalhamento()
{
    $responsavel = $_GET['responsavel'] ?? '';
    $tipo = $_GET['tipo'] ?? 'produzido';

    $model = new ResponsavelProducao();

    if ($tipo == 'cancelado') {
        $pedidos = $model->buscarCanceladosPorResponsavel($responsavel);
    } else {
        $pedidos = $model->buscarProduzidosPorResponsavel($responsavel);
    }

    require __DIR__ . '/../views/relatorios/detalhamento.php';
}

}