<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titulo            = mysqli_real_escape_string($con, trim($_POST['titulo']));
    $status            = $_POST['status'];
    $objetivo          = mysqli_real_escape_string($con, trim($_POST['objetivo']));
    $publico_alvo      = mysqli_real_escape_string($con, trim($_POST['publico_alvo']));
    $fk_eixo           = intval($_POST['fk_eixo']);
    $fk_periodicidade  = intval($_POST['fk_periodicidade']);
    $fk_espaco         = intval($_POST['fk_espaco']);
    $data_inicio       = $_POST['data_inicio'];
    $data_fim          = $_POST['data_fim'];    
    $observacoes       = mysqli_real_escape_string($con, trim($_POST['observacoes']));
    $visibilidade      = $_POST['visibilidade'];
    $quantidade        = intval($_POST['quantidade']);
    
    
    $nome_imagem_db = "";

    
    if (isset($_FILES['imagem_atividade']) && $_FILES['imagem_atividade']['error'] === UPLOAD_ERR_OK) {
        
        $nome_original = $_FILES['imagem_atividade']['name'];
        $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
        
        
        $extensoes_permitidas = ['jpg', 'jpeg', 'png'];

        if (in_array($extensao, $extensoes_permitidas)) {
            
            $nome_imagem_db = md5(uniqid(rand(), true)) . '.' . $extensao;
            
            
            $diretorio_destino = "../../img/img_atividades/" . $nome_imagem_db;

            
            if (!move_uploaded_file($_FILES['imagem_atividade']['tmp_name'], $diretorio_destino)) {
                
                $_SESSION['erro'] = true;
                header("location: ../");
                exit;
            }
        }
    }
    
    $con->begin_transaction();

    try {       

        
        $sql = mysqli_query($con, "INSERT INTO `atividade`(`titulo`, `objetivo`, `publico_alvo`, `status`, `data_inicio`, `data_fim`, `observacoes`, `fk_eixo`, `fk_periodicidade`, `fk_espaco`, `visibilidade`, `quantidade`, `img`) VALUES ('$titulo', '$objetivo', '$publico_alvo', '$status', '$data_inicio', '$data_fim', '$observacoes', '$fk_eixo', '$fk_periodicidade', '$fk_espaco', '$visibilidade', '$quantidade', '$nome_imagem_db')");
           
        if (!$sql) {
            throw new Exception("Erro ao inserir atividade principal.");
        }
        
        $id_atividade = $con->insert_id;
        
        if ($fk_eixo === 4) {
            
            $link_midia        = mysqli_real_escape_string($con, $_POST['link']);
            $detalhes          = mysqli_real_escape_string($con, $_POST['detalhes']);

            $sql_cine = mysqli_query($con, "INSERT INTO cine_biblioteca (link_midia, detalhes, fk_atividade) VALUES ('$link_midia', '$detalhes', '$id_atividade')");
           
            if (!$sql_cine) {
                throw new Exception("Erro ao inserir desdobramento do CineBiblioteca.");
            }
        }
        
        $con->commit();
        
        $_SESSION['sucesso'] = true;
        header("location: ../");
        exit;

    } catch (Exception $e) {        
        
        $con->rollback();        
        
        if (!empty($nome_imagem_db) && file_exists("../../img/img_atividades/" . $nome_imagem_db)) {
            unlink("../../img/img_atividades/" . $nome_imagem_db);
        }

        $_SESSION['erro'] = true;
        header("location: ../");
        exit;
    }
}
?>