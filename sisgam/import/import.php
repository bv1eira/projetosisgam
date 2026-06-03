<?php
require 'vendor/autoload.php';
require_once '../auth/session.php';
require_once '../conexao/conexao.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('America/Sao_Paulo');



function formatarDataBanco($data)
{
    $data = trim($data);
    if (empty($data) || $data == '00/00/0000' || $data == '-') {
        return '0000-00-00';
    }

    
    if (is_numeric($data)) {
        return date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data));
    }

    
    $d = DateTime::createFromFormat('d/m/Y', $data);
    if ($d && $d->format('d/m/Y') === $data) {
        return $d->format('Y-m-d');
    }

    
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return $data;
    }

    return '0000-00-00';
}

function lerPlanilha($arquivo)
{
    if (!file_exists($arquivo)) return [];
    return IOFactory::load($arquivo)->getActiveSheet()->toArray(null, true, true, true);
}


$arquivoExcel = 'dados.xlsx';
$dadosPlanilha = lerPlanilha($arquivoExcel);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SisMAB</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sql-debug { font-size: 10px; color: #666; background: #eee; padding: 5px; margin-bottom: 5px; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="container my-5">
        <h2 class="mb-4">Processando Importação de Atividades (Mãe)</h2>

        <?php
        $contagem = 0;

        if (!empty($dadosPlanilha)) {
            foreach ($dadosPlanilha as $i => $ln) {
                
                if ($i == 1 || empty($ln['A'])) continue;
                
                $titulo            = trim($ln['A']);
                $objetivo          = trim($ln['B']);
                $publico_alvo      = trim($ln['C']);
                $status            = (trim($ln['D']) === 'Executado') ? 'Executado' : 'Planejado';
                $data_inicio       = formatarDataBanco($ln['E']);
                $data_fim          = formatarDataBanco($ln['F']);
                $observacoes       = isset($ln['G']) ? trim($ln['G']) : '';
                $publico_previsto  = isset($ln['H']) ? intval($ln['H']) : 0;
                $publico_realizado = isset($ln['I']) ? intval($ln['I']) : 0;
                
                
                $nome_eixo         = isset($ln['J']) ? trim($ln['J']) : '';
                $nome_periodicidade= isset($ln['K']) ? trim($ln['K']) : '';
                $nome_espaco       = isset($ln['L']) ? trim($ln['L']) : '';
                $visibilidade      = (isset($ln['M']) && trim($ln['M']) === 'Interna') ? 'Interna' : 'Publica';                               
                
                $fk_eixo = 1;
                $stmt_e = $con->prepare("SELECT id_eixo FROM eixo_tematico WHERE nome_eixo LIKE ? LIMIT 1");
                $busca_e = "%" . $nome_eixo . "%";
                $stmt_e->bind_param("s", $busca_e);
                $stmt_e->execute();
                $res_e = $stmt_e->get_result();
                if ($res_e->num_rows > 0) $fk_eixo = $res_e->fetch_assoc()['id_eixo'];
                $stmt_e->close();
                
                $fk_periodicidade = 1; 
                $stmt_p = $con->prepare("SELECT id_periodicidade FROM periodicidade WHERE nome_periodicidade LIKE ? LIMIT 1");
                $busca_p = "%" . $nome_periodicidade . "%";
                $stmt_p->bind_param("s", $busca_p);
                $stmt_p->execute();
                $res_p = $stmt_p->get_result();
                if ($res_p->num_rows > 0) $fk_periodicidade = $res_p->fetch_assoc()['id_periodicidade'];
                $stmt_p->close();
                
                $fk_espaco = 1; 
                $stmt_esp = $con->prepare("SELECT id_espaco FROM espaco_fisico WHERE nome_espaco LIKE ? LIMIT 1");
                $busca_esp = "%" . $nome_espaco . "%";
                $stmt_esp->bind_param("s", $busca_esp);
                $stmt_esp->execute();
                $res_esp = $stmt_esp->get_result();
                if ($res_esp->num_rows > 0) $fk_espaco = $res_esp->fetch_assoc()['id_espaco'];
                $stmt_esp->close();


                
                $titulo_esc       = mysqli_real_escape_string($con, $titulo);
                $objetivo_esc     = mysqli_real_escape_string($con, $objetivo);
                $publico_alvo_esc = mysqli_real_escape_string($con, $publico_alvo);
                $observacoes_esc  = mysqli_real_escape_string($con, $observacoes);

                $sql = "INSERT INTO `atividade` (
                    `titulo`, `objetivo`, `publico_alvo`, `status`, 
                    `data_inicio`, `data_fim`, `observacoes`, `publico_previsto`, 
                    `publico_realizado`, `fk_eixo`, `fk_periodicidade`, `fk_espaco`, `visibilidade`
                ) VALUES (
                    '$titulo_esc', '$objetivo_esc', '$publico_alvo_esc', '$status', 
                    '$data_inicio', '$data_fim', '$observacoes_esc', $publico_previsto, 
                    $publico_realizado, $fk_eixo, $fk_periodicidade, $fk_espaco, '$visibilidade'
                )";

                
                if (mysqli_query($con, $sql)) {
                    $contagem++;
                    echo "<div class='text-success small'>✔️ Atividade <strong>$titulo</strong> importada com sucesso.</div>";
                } else {
                    echo "<div class='text-danger small'>❌ Erro na atividade $titulo: " . mysqli_error($con) . "</div>";
                    echo "<div class='sql-debug'>$sql</div>";
                }
                echo '<hr class="my-1">';
            }
        }

        echo "<div class='alert alert-success mt-4'>📊 Importação de Atividades concluída! <strong>$contagem</strong> registros inseridos.</div>";
        echo "<a href='../home/' class='btn btn-primary'>Voltar ao Painel</a>";
        ?>

    </div>
</body>
</html>