<?php
session_start();
session_regenerate_id(true);
function login($login, $senha)
{
    global $con, $notfy;
    
    // Ajuste para verificar se a consulta foi preparada com sucesso
    $sql = "SELECT * FROM usuarios WHERE usuario=? ";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt === false) {
        $notfy = 'var notyf = new Notyf({delay: 5000});'
            . 'notyf.alert("Erro na preparação da consulta!");';
        return;
    }

    // Bind dos parâmetros
    mysqli_stmt_bind_param($stmt, 's', $login);

    // Executa a consulta
    mysqli_stmt_execute($stmt);

    // Obtém o resultado
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) != 0) {
        $resp = mysqli_fetch_assoc($result);

        if (password_verify($senha, $resp['senha'])) {
            $_SESSION['nome'] = mb_convert_case($resp['usuario'], MB_CASE_TITLE);
            $_SESSION['email'] = $resp['email'];
            $_SESSION['usuario'] = $resp['usuario'];
            $_SESSION['admin'] = $resp['admin'];
            $_SESSION['timeout'] = strtotime('+2 hours');
            
            
            setcookie("sess", base64_encode(serialize($_SESSION)), 0, '/', ".gestambi.com.br", false, true);
            header("Location: http://gestambi.com.br/fotos/visualizar_fotos.php");
            exit;
        } else {
            $notfy = 'var notyf = new Notyf({delay: 5000});'
                . 'notyf.alert("Login ou senha incorreta, favor tentar novamente!");';
        }
    } else {
        $notfy = 'var notyf = new Notyf({delay: 5000});'
            . 'notyf.alert("Login ou senha incorreta, favor tentar novamente!");';
    }

    mysqli_close($con);
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/autoload.php';
    $con = connect_local_mysqli("gestao_ambiental");

    $notfy = "";

    if (isset($_POST['login_submit'])) {
        $login = htmlspecialchars(mb_convert_case($_POST['login'] ?? "", MB_CASE_UPPER));
        $senha = $_POST['senha'] ?? "";
        login($login, $senha);
    }
?>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height, initial-scale=1">
    <meta http-equiv="Content-Language" content="pt-br">
    <meta name="description" content="Sistema de Gestão Ambiental">
    <meta name="author" content="Sistema de Gestão Ambiental - Desenvolvido por Douglas Marcondes">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Sistema de Gestão Ambiental</title>
    <link rel="icon" href="/includes/logo.ico">
</head>

<style>
    * {
        box-sizing: border-box;
        outline: none;
    }

    .form-control {
        font-size: 16px;
        padding: 10px;
    }

    body {
        background-size: cover;
    }

    .login-form {
        margin-top: 60px;
    }

    form[role=login] {
        color: #5d5d5d;
        background: #f2f2f2;
        padding: 20px;
        border-radius: 10px;
    }

    form[role=login] img {
        display: block;
        margin: 0 auto;
    }

    form[role=login] input,
    form[role=login] button {
        font-size: 18px;
        margin: 16px 0;
    }

    form[role=login]>div {
        text-align: center;
    }

    .form-links {
        text-align: center;
        margin-top: 1em;
    }

    .form-links a {
        color: #5d5d5d;
        text-decoration: underline;
    }

    .login-button {
        display: block;
        margin: 0 auto;
        width: 100%;
    }
</style>

<body>
    <div class="container">
        <div class="row" id="pwd-container">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <section class="login-form">
                    <form method="post" action="" role="login">
                        <img class="logo" src="../includes/logo.ico" alt="Logo" style="max-height: 200px;">
                        <div id="title" style="font-family: 'Philosopher', sans-serif; font-size: 2.5em; text-align: center;">Sistema de Gestão Ambiental</div>
                        <input type="text" class="form-control input-lg" name="login" id="login" placeholder="Login" required="required" />
                        <input type="password" class="form-control input-lg" name="senha" id="senha" placeholder="Senha" required="required" />
                        <button type="submit" name="login_submit" class="btn btn-lg btn-success login-button">Login</button>
                        <div>
                            <a href="<?php echo HOME_URL; ?>login/recuperar_senha.php">Esqueci Minha Senha</a>
                        </div>
                    </form>
                    <div class="form-links">
                        <a href="<?php echo HOME_URL; ?>login/registrar.php">Não possui uma conta? Cadastre-se</a>
                    </div>
                </section>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</html>
