<?php
session_start();
require_once '../../auth/session.php';
require_once '../../conexao/conexao.php';

date_default_timezone_set('America/Sao_Paulo');
$agora = getdate();
$anoData = $agora["year"];
$mes = $agora["mon"];
$dia = $agora["mday"];
$hora = $agora["hours"];
$minutos = $agora["minutes"];
$segundos = $agora["seconds"];

$data_inscricao = $anoData."-".$mes."-".$dia." ".$hora.":".$minutos.":".$segundos;

echo $acao = $_POST['tipo'] ?? '';
$fk_id_usuario = $_SESSION['usuario_id'];

switch ($acao) {
    case '1':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fk_atividade = $_POST['id_atividade'];            

            $qtd = mysqli_fetch_assoc(mysqli_query($con, "SELECT count(*) AS qtd FROM inscricao_evento WHERE fk_id_usuario='$fk_id_usuario' AND fk_atividade='$fk_atividade'"))['qtd'];

            if($qtd==0){
                $stmt = mysqli_query($con, "INSERT INTO `inscricao_evento`(`fk_id_usuario`, `fk_atividade`, `data_inscricao`) VALUES ('$fk_id_usuario','$fk_atividade','$data_inscricao')");           

                if ($stmt){
                    $_SESSION['result']='1';
                    header("Location: ../");
                    exit();
                } else {
                    $_SESSION['result']='2';
                    header("Location: ../");
                    exit();
                }
            }else{
                $_SESSION['result']='2';
                header("Location: ../");
                exit();
            }
            
            
        }
        break;    

    case '2':
        $id_inscricao = intval($_POST['id_inscricao'] ?? 0);
       
        if ($id_inscricao > 0) {
            $stmt = mysqli_query($con, "DELETE FROM inscricao_evento WHERE id_inscricao = '$id_inscricao'");            

            if ($stmt){
                $_SESSION['result']='1';
                header("Location: ../");
                exit();
            } else {
                $_SESSION['result']='2';
                header("Location: ../");
                exit();
            }
        }
        break;

    default:
        header("Location: ../");
        break;
}
$con->close();