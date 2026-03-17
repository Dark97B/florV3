<?php
require_once __DIR__ . '/../../config/database.php';

class ResponsavelProducao {
    private $conn;
    private $table = 'responsavel_producao';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function criar($pedidoId, $tipo, $responsavel) {
    $sql = "INSERT INTO {$this->table} (pedido_id, tipo, responsavel, data_registro)
            VALUES (:pedido_id, :tipo, :responsavel, NOW())";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':pedido_id', $pedidoId);
    $stmt->bindValue(':tipo', $tipo);
    $stmt->bindValue(':responsavel', $responsavel);
    $stmt->execute();
}


    public function listarPorData($data) {
        $sql = "SELECT responsavel, COUNT(*) as quantidade 
                FROM {$this->table}
                WHERE data_registro = :data
                GROUP BY responsavel
                ORDER BY quantidade DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function listarPorPeriodo($inicio, $fim)
{
    $sql = "
        SELECT 
            responsavel,
            COUNT(*) FILTER (WHERE status <> 'Cancelado') AS quantidade_produzido,
            COUNT(*) FILTER (WHERE status = 'Cancelado') AS quantidade_cancelado
        FROM (
            -- ENTREGA
            SELECT responsavel_producao AS responsavel, status, data_abertura
            FROM pedidos_entrega

            UNION ALL

            -- PRONTA ENTREGA
            SELECT vendedor_codigo AS responsavel, status, data_abertura
            FROM pedidos_pronta_entrega

            UNION ALL

            -- RETIRADA
            SELECT responsavel_producao AS responsavel, status, data_abertura
            FROM pedidos_retirada
        ) AS todos_pedidos
        WHERE data_abertura BETWEEN :inicio AND :fim
        AND responsavel IS NOT NULL
        GROUP BY responsavel
        ORDER BY responsavel
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':inicio' => $inicio,
        ':fim' => $fim
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function listarDetalhes($responsavel, $tipo, $inicio, $fim)
{
    $filtroStatus = ($tipo === 'cancelado')
        ? "status = 'Cancelado'"
        : "status <> 'Cancelado'";

    $sql = "
        SELECT id, cliente, tipo, data_abertura
        FROM (
            SELECT id, remetente AS cliente, 'Entrega' AS tipo,
                   status, data_abertura, responsavel_producao AS responsavel
            FROM pedidos_entrega

            UNION ALL

            SELECT id, nome AS cliente, 'Retirada' AS tipo,
                   status, data_abertura, responsavel_producao AS responsavel
            FROM pedidos_retirada

            UNION ALL

            SELECT id, nome AS cliente, 'Pronta Entrega' AS tipo,
                   status, data_abertura, vendedor_codigo AS responsavel
            FROM pedidos_pronta_entrega
        ) AS todos
        WHERE responsavel = :responsavel
        AND $filtroStatus
        AND data_abertura BETWEEN :inicio AND :fim
        ORDER BY data_abertura DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':responsavel' => $responsavel,
        ':inicio' => $inicio,
        ':fim' => $fim
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

public function buscarCanceladosPorResponsavel($responsavel)
{
    $sql = "
        SELECT id, cliente, tipo, data_abertura
        FROM (
            SELECT id, remetente AS cliente, 'Entrega' AS tipo,
                   status, data_abertura, responsavel_producao AS responsavel
            FROM pedidos_entrega

            UNION ALL

            SELECT id, nome AS cliente, 'Retirada' AS tipo,
                   status, data_abertura, responsavel_producao AS responsavel
            FROM pedidos_retirada

            UNION ALL

            SELECT id, nome AS cliente, 'Pronta Entrega' AS tipo,
                   status, data_abertura, vendedor_codigo AS responsavel
            FROM pedidos_pronta_entrega
        ) AS todos
        WHERE responsavel = :responsavel
        AND status = 'Cancelado'
        ORDER BY data_abertura DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':responsavel', $responsavel);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}