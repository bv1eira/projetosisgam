<?php
session_start();
require_once '../auth/session.php';
require_once '../conexao/conexao.php';

unset($_SESSION['id']);

$usuario_nome  = $_SESSION['usuario_nome'];
$usuario_nivel = $_SESSION['usuario_nivel'];

if(!in_array($usuario_nivel, ['1','2','3'])){
    header("location: ../");
    exit;
}


if ($usuario_nivel == 1) {
    $texto_nivel = "Adm";
} elseif ($usuario_nivel == 2) {
    $texto_nivel = "Gestor";
} elseif ($usuario_nivel == 3) {
    $texto_nivel = "Colaborador";
} else {
    $texto_nivel = "Visitante";
}


if ($usuario_nivel === '1' || $usuario_nivel === '2') {
    $sql = "SELECT a.*, e.nome_eixo, p.nome_periodicidade, esp.nome_espaco, 
                   c.link_midia AS cine_link, c.detalhes AS cine_detalhes
            FROM atividade a
            INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
            INNER JOIN periodicidade p ON a.fk_periodicidade = p.id_periodicidade
            INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
            LEFT JOIN cine_biblioteca c ON c.fk_atividade = a.id_atividade
            ORDER BY a.data_inicio DESC";
} else {
    $sql = "SELECT a.*, e.nome_eixo, p.nome_periodicidade, esp.nome_espaco, 
                   c.link_midia AS cine_link, c.detalhes AS cine_detalhes
            FROM atividade a
            INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
            INNER JOIN periodicidade p ON a.fk_periodicidade = p.id_periodicidade
            INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
            LEFT JOIN cine_biblioteca c ON c.fk_atividade = a.id_atividade
            WHERE a.visibilidade = 'Publica'
            ORDER BY a.data_inicio DESC";
}

$dados_atividades = $con->query($sql);

if (!$dados_atividades) {
    die("<h3>Erro na Consulta SQL Principal do SisMAB:</h3>" . $con->error . "<br><br><strong>Query executada:</strong><br>" . $sql);
}

$total_atividades = $dados_atividades->num_rows;


$query_planejado = $con->query("SELECT COUNT(*) as qtd FROM atividade WHERE status = 'Planejado'");
$total_planejado = $query_planejado ? $query_planejado->fetch_assoc()['qtd'] : 0;

$query_executado = $con->query("SELECT COUNT(*) as qtd FROM atividade WHERE status = 'Executado'");
$total_executado = $query_executado ? $query_executado->fetch_assoc()['qtd'] : 0;


$sql_grafico1 = "SELECT e.nome_eixo, COUNT(a.id_atividade) AS total 
                 FROM eixo_tematico e 
                 LEFT JOIN atividade a ON a.fk_eixo = e.id_eixo ";
if ($usuario_nivel !== '1') {
    $sql_grafico1 .= " AND a.visibilidade = 'Publica' ";
}
$sql_grafico1 .= " GROUP BY e.nome_eixo";

$res_g1 = $con->query($sql_grafico1);
$eixos_labels = [];
$eixos_valores = [];
if ($res_g1) {
    while ($g1 = $res_g1->fetch_assoc()) {
        $eixos_labels[]  = $g1['nome_eixo'];
        $eixos_valores[] = (int)$g1['total'];
    }
}


$sql_grafico2 = "SELECT esp.nome_espaco, 
                        SUM(CASE WHEN a.status = 'Planejado' THEN 1 ELSE 0 END) AS total_planejado, 
                        SUM(CASE WHEN a.status = 'Executado' THEN 1 ELSE 0 END) AS total_executado 
                 FROM espaco_fisico esp 
                 LEFT JOIN atividade a ON a.fk_espaco = esp.id_espaco ";
if (!in_array($usuario_nivel, ['1', '2'])) {
    $sql_grafico2 .= " AND a.visibilidade = 'Publica' ";
}
$sql_grafico2 .= " GROUP BY esp.nome_espaco";

$res_g2 = $con->query($sql_grafico2);
$espacos_labels = [];
$total_planejado_valores = [];
$total_executado_valores = [];
if ($res_g2) {
    while ($g2 = $res_g2->fetch_assoc()) {
        $espacos_labels[]          = $g2['nome_espaco'];
        $total_planejado_valores[] = (int)$g2['total_planejado'];
        $total_executado_valores[] = (int)$g2['total_executado'];
    }
}


$sql_grafico3 = "SELECT e.nome_eixo, SUM(IFNULL(a.quantidade, 0)) AS total_publico 
                 FROM eixo_tematico e 
                 LEFT JOIN atividade a ON a.fk_eixo = e.id_eixo ";
if (!in_array($usuario_nivel, ['1', '2'])) {
    $sql_grafico3 .= " AND a.visibilidade = 'Publica' ";
}
$sql_grafico3 .= " GROUP BY e.nome_eixo";

$res_g3 = $con->query($sql_grafico3);
$eixos_publico_labels = [];
$eixos_publico_valores = [];
if ($res_g3) {
    while ($g3 = $res_g3->fetch_assoc()) {
        $eixos_publico_labels[]  = $g3['nome_eixo'];
        $eixos_publico_valores[] = (int)$g3['total_publico'];
    }
}
?>