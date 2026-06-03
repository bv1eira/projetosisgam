<?php
require_once '../conexao/conexao.php';
session_start();

date_default_timezone_set('America/Sao_Paulo');
$agora = getdate();


$anoData = $agora["year"];
$mes = $agora["mon"];
$dia = $agora["mday"];
$hora = $agora["hours"];
$minutos = $agora["minutes"];
$segundos = $agora["seconds"];

$data_criacao = $anoData."-".$mes."-".$dia." ".$hora.":".$minutos.":".$segundos;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = $_POST['tipo'];
    if($tipo==1){

        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        if (empty($email) || empty($senha)) {
            $_SESSION['login_erro'] = "Preencha todos os campos.";
            header("Location: ../");
            exit;
        }

        
        $sql = "SELECT id_usuario, nome, email, senha, nivel_permissao FROM usuario WHERE email = ? LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            
            if ($senha == $usuario['senha']) {
                
                
                session_regenerate_id(true);

                
                $_SESSION['usuario_id']     = $usuario['id_usuario'];
                $_SESSION['usuario_nome']   = $usuario['nome'];
                $_SESSION['usuario_nivel']  = $usuario['nivel_permissao'];
                $_SESSION['usuario_logado'] = true;

                if($usuario['nivel_permissao']==1 || $usuario['nivel_permissao']==2 || $usuario['nivel_permissao']==3){
                    header("Location: ../home/");
                    exit;
                }else{
                    header("Location: ../atividades/");
                    exit;
                }
                
            }
        }

        
        $_SESSION['login_erro'] = "E-mail ou senha incorretos.";
        header("Location: ../");
        exit;

    }else{
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);
        $idade = trim($_POST['idade']);
        $sexo = trim($_POST['sexo']);


        
        $sql = "SELECT * FROM usuario WHERE email = ? LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {                          
                
                session_regenerate_id(true);

                $sqlinsert = mysqli_query($con, "INSERT INTO `usuario`(`nome`, `email`, `senha`, `nivel_permissao`, `data_criacao`, `idade`, `sexo`) 
                    VALUES ('$nome','$email','$senha','4','$data_criacao','$idade','$sexo')");

                $id_usuario = $con->insert_id;
                
                $_SESSION['usuario_id']     = $id_usuario;
                $_SESSION['usuario_nome']   = $nome;
                $_SESSION['usuario_nivel']  = '4';
                $_SESSION['usuario_logado'] = true;
                
                header("Location: ../atividades/");
                exit;
            
        }else{
            $_SESSION['login_erro'] = "Cadastro existente.";
            header("Location: ../cadastro/");
            exit;
        }
    }

        
        
    
    
} else {
    header("Location: ../");
    exit;
}