<!DOCTYPE html>
<html lang="pt-br">
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
    <form action="<?= URL_BASE ?>/funcionario/lista" method="post">
        <label for="nome_completo">Nome Completo:</label><br>
        <input type="text" id="nome_completo" name="nome_completo"><br><br>

        <label for="data_nascimento">Data de Nascimento:</label><br>
        <input type="date" id="data_nascimento" name="data_nascimento"><br><br>

        <label for="cpf">CPF:</label><br>
        <input type="text" id="cpf" name="cpf"><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" id="email" name="email"><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha"><br><br>

        <label for="numero_telefone">Número de Telefone:</label><br>
        <input type="text" id="numero_telefone" name="numero_telefone"><br><br>

        <label for="id_empresa">Empresa (ID):</label><br>
        <input type="number" id="id_empresa" name="id_empresa"><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>