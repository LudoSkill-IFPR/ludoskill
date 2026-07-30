<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <? if($_POST['erros']){
        print($_POST['erros']);   
    }
    ?>
    <form action="<?= URL_BASE ?>/funcionario/salvar" method="post">
        <label for="nomeCompleto">Nome Completo:</label><br>
        <input type="text" id="nomeCompleto" name="nomeCompleto"><br><br>

        <label for="dataNascimento">Data de Nascimento:</label><br>
        <input type="date" id="dataNascimento" name="dataNascimento"><br><br>

        <label for="cpf">CPF:</label><br>
        <input type="text" id="cpf" name="cpf"><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" id="email" name="email"><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha"><br><br>

        <label for="numeroTelefone">Número de Telefone:</label><br>
        <input type="text" id="numeroTelefone" name="numeroTelefone"><br><br>

        <label for="empresa">Empresa (ID):</label><br>
        <input type="number" id="empresa" name="empresa"><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>