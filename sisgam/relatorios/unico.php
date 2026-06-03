<?php

if (ob_get_length()) ob_clean();
ob_start();

require_once '../auth/session.php';
require_once '../conexao/conexao.php';
include('../mpdf60/mpdf.php');

date_default_timezone_set('America/Sao_Paulo');


if (!isset($_POST['id']) || empty($_POST['id'])) {
    die("Erro: Controle de identificação da atividade ausente.");
}

$id_atividade = intval($_POST['id']);

$sql = "SELECT a.*, e.nome_eixo, p.nome_periodicidade, esp.nome_espaco, 
               c.link_midia AS cine_link, c.detalhes AS cine_detalhes
        FROM atividade a
        INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
        INNER JOIN periodicidade p ON a.fk_periodicidade = p.id_periodicidade
        INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
        LEFT JOIN cine_biblioteca c ON c.fk_atividade = a.id_atividade
        WHERE a.id_atividade = ? LIMIT 1";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id_atividade);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Erro: Atividade não localizada no sistema de arquivos.");
}

$atv = $resultado->fetch_assoc();
$stmt->close();


$html = '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #222; font-size: 11px; margin: 0; padding: 0; }        
        
        .w-100 { width: 100%; }
        .table-border { border-collapse: collapse; margin-bottom: 15px; }
        .table-border td, .table-border th { border: 1px solid #1e3a8a; padding: 8px; vertical-align: top; }        
        
        .bg-navy { background-color: #003366; color: #ffffff; }
        .bg-light-blue { background-color: #f0f4f8; font-weight: bold; width: 25%; color: #003366; }        
        
        .banner-title-container { background-color: #003366; color: #ffffff; text-align: center; padding: 15px 10px; margin-bottom: 20px; }
        .main-title { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .sub-title { font-size: 13px; font-weight: bold; text-transform: uppercase; margin-top: 5px; color: #e2e8f0; }
        
        .section-header { background-color: #003366; color: #ffffff; font-weight: bold; padding: 6px 10px; font-size: 11px; text-transform: uppercase; margin-top: 15px; letter-spacing: 0.5px; }        
        
        .content-box { border: 1px solid #003366; padding: 12px; text-align: justify; line-height: 1.6; background-color: #fafafa; margin-bottom: 15px; }        
        
        .signature-container { margin-top: 30px; width: 100%; border-collapse: collapse; }
        .signature-box { border-top: 1px solid #333; text-align: center; padding-top: 5px; font-size: 10px; }
        
        .footer-banner { text-align: center; margin-top: 25px; padding-top: 10px; border-top: 2px solid #003366; font-size: 9px; color: #555; }
    </style>
</head>
<body>

    <div style="text-align: center; margin-bottom: 10px;">
        <img src="../img/banner.png" style="width: 100%; max-height: 80px; object-fit: contain;">
    </div>

    <div class="banner-title-container">
        <div class="main-title">Relatório de Evento Institucional</div>
        <div class="sub-title">Resumo Executivo de Atividade</div>
    </div>

    <div class="section-header"><i class="fa-solid fa-list"></i> Dados Rápidos da Ação</div>
    <table class="w-100 table-border" style="margin-top: 5px;">
        <tr>
            <td class="bg-light-blue">Título do Evento:</td>
            <td colspan="3" style="font-size: 12px; font-weight: bold; color: #003366;">' . htmlspecialchars($atv['titulo']) . '</td>
        </tr>
        <tr>
            <td class="bg-light-blue">Eixo Temático:</td>
            <td width="25%">' . htmlspecialchars($atv['nome_eixo']) . '</td>
            <td class="bg-light-blue">Periodicidade:</td>
            <td width="25%">' . htmlspecialchars($atv['nome_periodicidade']) . '</td>
        </tr>
        <tr>
            <td class="bg-light-blue">Local / Espaço:</td>
            <td>' . htmlspecialchars($atv['nome_espaco']) . '</td>
            <td class="bg-light-blue">Público Atendido:</td>
            <td style="font-weight: bold; font-size: 12px;">' . intval($atv['quantidade']) . ' participantes</td>
        </tr>
        <tr>
            <td class="bg-light-blue">Data de Início:</td>
            <td>' . date('d/m/Y', strtotime($atv['data_inicio'])) . '</td>
            <td class="bg-light-blue">Data de Término:</td>
            <td>' . date('d/m/Y', strtotime($atv['data_fim'])) . '</td>
        </tr>
        <tr>
            <td class="bg-light-blue">Status Operacional:</td>
            <td style="font-weight: bold;">' . ($atv['status'] === 'Planejado' ? 'Em Planejamento' : 'Executado / Concluído') . '</td>
            <td class="bg-light-blue">Escopo Visibilidade:</td>
            <td>' . ($atv['visibilidade'] === 'Interna' ? 'Restrita (Midiateca)' : 'Pública (Unidade)') . '</td>
        </tr>
    </table>

    <div class="section-header">Resumo do Evento e Objetivos</div>
    <div class="content-box">' . nl2br(htmlspecialchars($atv['objetivo'])) . '</div>

    <div class="section-header">Público-Alvo e Segmentação</div>
    <div class="content-box">' . nl2br(htmlspecialchars($atv['publico_alvo'])) . '</div>

    ';
    if (!empty($atv['cine_link'])) {
        $html .= '
        <div class="section-header" style="background-color: #d97706;">Desdobramento: Parâmetros do CineBiblioteca</div>
        <table class="w-100 table-border" style="margin-top: 5px; margin-bottom: 0px;">
            <tr>
                <td class="bg-light-blue" style="width: 20%; background-color: #fffbeb;">Mídia / Link:</td>
                <td style="background-color: #fffbeb;"><a href="' . htmlspecialchars($atv['cine_link']) . '" style="color: #0056b3; font-weight: bold;">' . htmlspecialchars($atv['cine_link']) . '</a></td>
            </tr>
        </table>
        <div class="content-box" style="border-top: none; background-color: #fffbeb;">
            <strong>Síntese / Resumo da Obra Exibida:</strong><br><br>
            ' . nl2br(htmlspecialchars($atv['cine_detalhes'])) . '
        </div>';
    }

    $html .= '
    <div class="section-header">Observações / Notas de Campo</div>
    <div class="content-box">' . (!empty($atv['observacoes']) ? nl2br(htmlspecialchars($atv['observacoes'])) : '<span style="color: #888; font-style: italic;">Nenhuma observação complementar registrada nesta ação pedagógica.</span>') . '</div>

    <table class="signature-container">
        <tr>
            <td width="45%" class="signature-box">
                <strong>Prof. ' . htmlspecialchars($_SESSION['usuario_nome']) . '</strong><br>
                Midiateca / Biblioteca SENAI Sapucaí
            </td>
            <td width="10%">&nbsp;</td>
            <td width="45%" class="signature-box">
                <strong>FIRJAN SENAI Sapucaí</strong><br>
                Coordenação de Gestão Escolar Técnica
            </td>
        </tr>
    </table>

    <div class="footer-banner">
        FALE CONOSCO: (21) 3333-8888 | contato@firjan.com.br | www.firjan.com.br<br>
        <span style="font-weight: bold; color: #003366; font-size: 8px; margin-top: 4px; d-block;">SisMAB • Sistema de Monitoramento de Atividades da Biblioteca</span>
    </div>

</body>
</html>';



$arquivo = "Relatorio_Evento_MAB_" . $id_atividade . ".pdf";

$mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 15, 6, 6);
$mpdf->SetDisplayMode('fullpage');

$mpdf->allow_charset_conversion = true;
$mpdf->charset_in = 'UTF-8';

$mpdf->setFooter('FIRJAN SENAI SAPUCAÍ | Relatório Técnico | Página {PAGENO} de {nbpg}');
$mpdf->WriteHTML($html); 

ob_clean();

$mpdf->Output($arquivo, 'D');
exit;
?>