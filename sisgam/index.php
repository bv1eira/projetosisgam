<?php
session_start();

$email_get = '';
$senha_get = '';


if (isset($_GET['usr']) && !empty($_GET['usr'])) {
    $parametro_completo = $_GET['usr'];
    
    if (strpos($parametro_completo, '-') !== false) {        
        $partes = explode('-', $parametro_completo);
        $email_get = htmlspecialchars($partes[0]);
        $senha_get = htmlspecialchars($partes[1]);
    } else {        
        $email_get = htmlspecialchars($parametro_completo);
        $senha_get = isset($_GET['pwd']) ? htmlspecialchars($_GET['pwd']) : '';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
</head>
<body>

<div class="card-login p-4 m-3">
    <div class="text-center mb-4">
        <img src="img/logo.png" width="200">
        <span class="text-muted d-block small">Sistema de Gestão de Atividades da Midiateca</span>
        <span class="badge bg-secondary mt-2">FIRJAN | SENAI</span>
    </div>

    <?php if (isset($_SESSION['login_erro'])): ?>
        <div class="alert alert-danger text-center py-2 small" role="alert">
            <?= $_SESSION['login_erro']; ?>
            <?php unset($_SESSION['login_erro']); ?>
        </div>
    <?php endif; ?>

    <form action="auth/" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">E-mail Corporativo</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@instituicao.com.br" value="<?= $email_get; ?>" required autocomplete="email">
        </div>

        <div class="mb-4">
            <label for="senha" class="form-label fw-semibold">Senha de Acesso</label>
            <div class="input-group">
                <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" value="<?= $senha_get; ?>" required autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" id="btn-olho">
                    <i class="bi bi-eye" id="icone-olho"></i>
                </button>
            </div>
        </div>

        <div class="d-grid mb-2">
            <input type="hidden" name="tipo" value="1">
            <button type="submit" class="btn btn-firjan py-2 fw-bold">Entrar no Sistema</button>
        </div>
    </form>

    <div class="footer-links">
        <span>Caso seja visitante? <a href="cadastro/">Cadastre-se</a></span>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('btn-olho').addEventListener('click', function() {
        const inputSenha = document.getElementById('senha');
        const iconeOlho = document.getElementById('icone-olho');
        
        if (inputSenha.type === 'password') {
            inputSenha.type = 'text';
            iconeOlho.className = 'bi bi-eye-slash';
        } else {
            inputSenha.type = 'password';
            iconeOlho.className = 'bi bi-eye';
        }
    });

    function setlogin(i) {
        if (i == 1) {
            document.getElementById('email').value = 'adm@biblioteca.com.br';
            document.getElementById('senha').value = 'abc1234';
        } else {
            document.getElementById('email').value = 'coord@instituicao.com.br';
            document.getElementById('senha').value = 'abc1234';
        }
    }
</script>
</body>
</html>