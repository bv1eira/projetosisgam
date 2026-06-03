<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
</head>
<body>

<div class="card-login p-4 m-3">
    <div class="text-center mb-4">
        <img src="../img/logo.png" width="200">
        <span class="text-muted d-block small">Formulário de cadastro</span>
        <span class="badge bg-secondary mt-2">FIRJAN | SENAI</span>
    </div>

    <?php if (isset($_SESSION['login_erro'])): ?>
        <div class="alert alert-danger text-center py-2 small" role="alert">
            <?= $_SESSION['login_erro']; ?>
            <?php unset($_SESSION['login_erro']); ?>
        </div>
    <?php endif; ?>

    <form action="../auth/" method="POST">
        <div class="row">
            <div class="col-sm-12">
                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="..." required >
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mail Corporativo</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@instituicao.com.br" required autocomplete="email">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-4">
                    <label for="senha" class="form-label fw-semibold">Senha de Acesso</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="btn-olho">
                            <i class="bi bi-eye" id="icone-olho"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="idade" class="form-label fw-semibold">Idade</label>
                    <input type="number" class="form-control" id="idade" name="idade" placeholder="..." required >
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="sexo" class="form-label fw-semibold">Sexo</label>
                    <select class="form-control" name="sexo" required="">
                        <option value="">--selecione--</option>
                        <option value="1">Masculino</option>
                        <option value="2">Feminino</option>
                        <option value="3">Outros</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="d-grid mb-2">
                    <input type="hidden" name="tipo" value="2">
                    <button type="submit" class="btn btn-firjan py-2 fw-bold">Cadastrar</button>
                </div>
            </div>
        </div>
        
    </form>

    <div class="footer-links">
        <span>Já tem cadastro? <a href="../">Voltar</a></span>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('btn-olho').addEventListener('click', function() {
        const inputSenha = document.getElementById('senha');
        const iconeOlho = document.getElementById('icone-olho');
        
        if (inputSenha.type === 'password') {
            inputSenha.type = 'text';
            
            iconeOlho.classList.remove('bi-eye');
            iconeOlho.classList.add('bi-eye-slash');
        } else {
            inputSenha.type = 'password';
            
            iconeOlho.classList.remove('bi-eye-slash');
            iconeOlho.classList.add('bi-eye');
        }
    });   
        
</script>
</body>
</html>