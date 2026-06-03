<?php
require 'vendor/autoload.php';
require_once '../auth/session.php';
require_once '../conexao/conexao.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('America/Sao_Paulo');

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
        <h2 class="mb-4">Processando Vínculos do CineBiblioteca (Tabela Filha)</h2>

        <?php
        $contagem = 0;

        if (!empty($dadosPlanilha)) {
            foreach ($dadosPlanilha as $i => $ln) {
                
                if ($i == 1 || empty($ln['B'])) continue;
                
                $titulo_atividade_mae = trim($ln['A']); 
                $titulo_midia         = trim($ln['B']);
                $link_midia           = trim($ln['C']);
                $detalhes             = isset($ln['D']) ? trim($ln['D']) : '';

                
                $fk_atividade = 0;
                $stmt_atv = $con->prepare("SELECT id_atividade FROM atividade WHERE titulo = ? ORDER BY id_atividade DESC LIMIT 1");
                $stmt_atv->bind_param("s", $titulo_atividade_mae);
                $stmt_atv->execute();
                $res_atv = $stmt_atv->get_result();
                
                if ($res_atv->num_rows > 0) {
                    $fk_atividade = $res_atv->fetch_assoc()['id_atividade'];
                }
                $stmt_atv->close();

                
                if ($fk_atividade === 0) {
                    echo "<div class='text-warning small'>⚠️ Mídia [<strong>$titulo_midia</strong>] ignorada: A atividade mãe '$titulo_atividade_mae' não foi localizada.</div><hr class='my-1'>";
                    continue;
                }

                
                $titulo_midia_esc = mysqli_real_escape_string($con, $titulo_midia);
                $link_midia_esc   = mysqli_real_escape_string($con, $link_midia);
                $detalhes_esc     = mysqli_real_escape_string($con, $detalhes);

                
                $sql = "INSERT INTO `cine_biblioteca` (
                    `titulo_midia`, `link_midia`, `detalhes`, `fk_atividade`
                ) VALUES (
                    '$titulo_midia_esc', '$link_midia_esc', '$detalhes_esc', $fk_atividade
                )";

                if (mysqli_query($con, $sql)) {
                    $contagem++;
                    echo "<div class='text-success small'>🎬 Mídia <strong>$titulo_midia</strong> vinculada com sucesso ao projeto mãe.</div>";
                } else {
                    echo "<div class='text-danger small'>❌ Erro ao vincular mídia $titulo_midia: " . mysqli_error($con) . "</div>";
                    echo "<div class='sql-debug'>$sql</div>";
                }
                echo '<hr class="my-1">';
            }
        }

        echo "<div class='alert alert-success mt-4'>📊 Vínculos do CineBiblioteca concluídos! <strong>$contagem</strong> mídias associadas.</div>";
        echo "<a href='../home/' class='btn btn-primary'>Voltar ao Painel</a>";
        ?>

    </div>
</body>
</html>