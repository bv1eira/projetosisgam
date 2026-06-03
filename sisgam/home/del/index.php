<?php
session_start();
require_once '../../auth/session.php';
require_once '../../conexao/conexao.php';


if ($_SESSION['usuario_nivel'] !== '1') {
    echo "<script>alert('Acesso negado. Você não tem permissão para realizar esta operação.'); window.location.href='../';</script>";
    exit;
}


if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_atividade = intval($_GET['id']);

    
    $sql = "DELETE FROM atividade WHERE id_atividade = ?";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $id_atividade);
        
        if ($stmt->execute()) {
            
            if ($stmt->affected_rows > 0) {
                //echo "<script>alert('Atividade excluída com sucesso!'); window.location.href='../';</script>";
                $_SESSION['sucesso']=true;
                header("location: ../");
                exit;
            } else {
                $_SESSION['erro']=true;
                header("location: ../");
                exit;
                //echo "<script>alert('Aviso: O registro não foi encontrado ou já havia sido removido.'); window.location.href='../';</script>";
            }
        } else {
            $_SESSION['erro']=true;
            header("location: ../");
            exit;
            //echo "<script>alert('Erro ao executar a exclusão no banco de dados.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        $_SESSION['erro']=true;
        header("location: ../");
        exit;
        //echo "<script>alert('Erro na preparação da consulta de exclusão.'); window.history.back();</script>";
    }
} else {
    $_SESSION['erro']=true;
    header("location: ../");
    exit;
    //echo "<script>alert('ID de atividade inválido ou não informado.'); window.location.href='../';</script>";
}

$con->close();
?>