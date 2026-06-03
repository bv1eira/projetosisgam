<?php
session_start();

require_once '../../auth/session.php';
require_once '../../conexao/conexao.php';

// if ($_SESSION['usuario_nivel'] !== '1' || $_SESSION['usuario_nivel'] !== '2') {
//     echo "<script>alert('Acesso negado.'); window.location.href='../../home/';</script>";
//     exit;
// }
if(!in_array($_SESSION['usuario_nivel'], ['1','2'])){
    header("location: ../../home/");
    exit;
}

if(isset($_POST['update_ativ'])){

    $id_atividade      = intval($_POST['id_atividade']);
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
    
    $nova_imagem_salva = false;
    $nome_imagem_db    = null;
    $imagem_antiga     = "";

    
    $con->begin_transaction();

    try {
        
        if (isset($_FILES['imagem_atividade']) && $_FILES['imagem_atividade']['error'] === UPLOAD_ERR_OK) {
            
            $nome_original = $_FILES['imagem_atividade']['name'];
            $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png'];

            if (in_array($extensao, $extensoes_permitidas)) {
                
                
                $busca_antiga = $con->query("SELECT img FROM atividade WHERE id_atividade = '$id_atividade' LIMIT 1");
                if ($busca_antiga && $busca_antiga->num_rows > 0) {
                    $imagem_antiga = $busca_antiga->fetch_assoc()['img'];
                }

                
                $nome_imagem_db = md5(uniqid(rand(), true)) . '.' . $extensao;
                $diretorio_destino = "../../img/img_atividades/" . $nome_imagem_db;

                if (move_uploaded_file($_FILES['imagem_atividade']['tmp_name'], $diretorio_destino)) {
                    $nova_imagem_salva = true;
                } else {
                    throw new Exception("Falha ao mover o novo arquivo de imagem.");
                }
            }
        }

        
        if ($nova_imagem_salva) {
            $sql_update = $con->query("UPDATE atividade SET 
                titulo = '$titulo', 
                objetivo = '$objetivo', 
                publico_alvo = '$publico_alvo', 
                status = '$status', 
                data_inicio = '$data_inicio', 
                data_fim = '$data_fim', 
                observacoes = '$observacoes',                       
                fk_eixo = '$fk_eixo', 
                fk_periodicidade = '$fk_periodicidade', 
                fk_espaco = '$fk_espaco', 
                visibilidade = '$visibilidade',
                quantidade = '$quantidade',
                img = '$nome_imagem_db' 
               WHERE id_atividade = '$id_atividade'");
        } else {
            $sql_update = $con->query("UPDATE atividade SET 
                titulo = '$titulo', 
                objetivo = '$objetivo', 
                publico_alvo = '$publico_alvo', 
                status = '$status', 
                data_inicio = '$data_inicio', 
                data_fim = '$data_fim', 
                observacoes = '$observacoes',                       
                fk_eixo = '$fk_eixo', 
                fk_periodicidade = '$fk_periodicidade', 
                fk_espaco = '$fk_espaco', 
                visibilidade = '$visibilidade',
                quantidade = '$quantidade' 
               WHERE id_atividade = '$id_atividade'");
        }

        if (!$sql_update) {
            throw new Exception("Erro ao atualizar os dados da atividade.");
        }

        
        if ($fk_eixo === 4) {
                
            $link_midia = mysqli_real_escape_string($con, $_POST['link']);
            $detalhes   = mysqli_real_escape_string($con, $_POST['detalhes']);

            $check_cine = $con->query("SELECT id_cine FROM cine_biblioteca WHERE fk_atividade = '$id_atividade' LIMIT 1");          
            $res_cine   = $check_cine->num_rows;

            if ($res_cine === 1) {
                $sql_cine = $con->query("UPDATE cine_biblioteca SET link_midia = '$link_midia', detalhes = '$detalhes' WHERE fk_atividade = '$id_atividade'");   
            } else {
                $sql_cine = $con->query("INSERT INTO cine_biblioteca (link_midia, detalhes, fk_atividade) VALUES ('$link_midia', '$detalhes', '$id_atividade')");               
            }

            if (!$sql_cine) {
                throw new Exception("Erro ao salvar dados complementares do CineBiblioteca.");
            }

        } else {            
            $con->query("DELETE FROM cine_biblioteca WHERE fk_atividade = '$id_atividade'");            
        }        
        
        
        $con->commit();

        
        if ($nova_imagem_salva && !empty($imagem_antiga)) {
            $caminho_foto_antiga = "../../img/img_atividades/" . $imagem_antiga;
            if (file_exists($caminho_foto_antiga)) {
                unlink($caminho_foto_antiga);
            }
        }

        $_SESSION['sucesso'] = true;
        $_SESSION['id'] = $id_atividade;
        header("location: ../");
        exit;

    } catch (Exception $e) {
        
        $con->rollback();

        
        if ($nova_imagem_salva && file_exists("../../img/img_atividades/" . $nome_imagem_db)) {
            unlink("../../img/img_atividades/" . $nome_imagem_db);
        }

        $_SESSION['erro'] = true;
        $_SESSION['id'] = $id_atividade;
        header("location: ../");
        exit;
    }

} else { 
    header("Location: ../../home/");
    exit;
}
?>