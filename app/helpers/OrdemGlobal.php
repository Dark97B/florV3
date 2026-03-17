<?php
require_once __DIR__ . '/../../config/database.php';

class OrdemGlobal {

    public static function getProximaOrdem() {

        $db = new Database();
        $conn = $db->getConnection();

        $conn->beginTransaction();

        $stmt = $conn->query("SELECT proxima_ordem FROM ordem_fila_global WHERE id = 1 FOR UPDATE");

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            $conn->rollBack();
            throw new Exception("Registro de ordem global não encontrado.");
        }

        $atual = $resultado['proxima_ordem'];

        $stmt = $conn->prepare("UPDATE ordem_fila_global SET proxima_ordem = proxima_ordem + 1 WHERE id = 1");
        $stmt->execute();

        $conn->commit();

        return $atual;
    }
}