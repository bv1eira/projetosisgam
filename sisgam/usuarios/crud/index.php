<?php
session_start();
require_once '../../auth/session.php';
require_once '../../conexao/conexao.php';

if ($_SESSION['usuario_nivel'] !== '1') {
    header("Location: ../../home/");
    exit();
}

date_default_timezone_set('America/Sao_Paulo');
$agora = getdate();
$anoData = $agora["year"];
$mes = $agora["mon"];
$dia = $agora["mday"];
$hora = $agora["hours"];
$minutos = $agora["minutes"];
$segundos = $agora["seconds"];

$data_criacao = $anoData."-".$mes."-".$dia." ".$hora.":".$minutos.":".$segundos;

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $senha = $_POST['senha'];
            $nivel = $_POST['nivel_permissao'];
            $idade = $_POST['idade'];
            $sexo = $_POST['sexo'];

            
            $stmt = mysqli_query($con, "INSERT INTO usuario (nome, email, senha, nivel_permissao, data_criacao, `idade`, `sexo`) VALUES ('$nome', '$email', '$senha', '$nivel', '$data_criacao', '$idade', '$sexo')");           

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

    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id    = intval($_POST['id']);
            $nome  = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $nivel = $_POST['nivel_permissao'];
            $senha = trim($_POST['senha']);
            $idade = $_POST['idade'];
            $sexo  = $_POST['sexo'];

            if (!empty($senha)) {
                $senha_crypto = $senha;
                $stmt = mysqli_query($con, "UPDATE usuario SET nome = '$nome', email = '$email', senha = '$senha_crypto', nivel_permissao = '$nivel', idade = '$idade', sexo = '$sexo'  WHERE id_usuario = '$id'");                
            } else {
                $stmt = mysqli_query($con, "UPDATE usuario SET nome = '$nome', email = '$email', idade = '$idade', sexo = '$sexo'  WHERE id_usuario = '$id'");
                
            }

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

    case 'deletar':
        $id = intval($_GET['id'] ?? 0);

        if ($id === intval($_SESSION['usuario_id'] ?? 0)) { 
            $_SESSION['result']='2';
            header("Location: ../");
            exit();
        }

        if ($id != '') {
            $del  =  mysqli_query($con, "DELETE FROM `inscricao_evento` WHERE fk_id_usuario ='$id'");
            $stmt =  mysqli_query($con, "DELETE FROM `usuario` WHERE id_usuario='$id'");            

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