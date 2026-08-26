<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">
    
    <title>Cadastro de Funcionários</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
        
        <h1 class="">Cadastro de Funcionarios</h1>
        <p class="mensagem">Provisório</p>

            <form class="card" action="" method="post">
                <div class="formgroup">
                    <label for="nome_completo">Nome</label>
                    <input class="card-secundario" type="text" id="nome_completo" name="nome_completo" value="<?= isset($funcionario['nome_completo']) ? htmlspecialchars($funcionario['nome_completo']) : '' ?>">
                    <?php if (isset($erros['nome_completo'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['nome_completo']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input class="card-secundario" type="date" id="data_nascimento" name="data_nascimento" value="<?= isset($funcionario['data_nascimento']) ? htmlspecialchars($funcionario['data_nascimento']) : '' ?>">
                    <?php if (isset($erros['data_nascimento'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['data_nascimento']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="cpf" class="form-label">CPF</label>
                    <input class="card-secundario" type="text" id="cpf" name="cpf" maxlength="18" value="<?= isset($funcionario['cpf']) ? htmlspecialchars($funcionario['cpf']) : '' ?>">
                    <?php if (isset($erros['cpf'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['cpf']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="email" class="form-label">E-mail</label>
                    <input class="card-secundario" type="email" id="email" name="email" value="<?= isset($funcionario['email']) ? htmlspecialchars($funcionario['email']) : '' ?>">
                    <?php if (isset($erros['email'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['email']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="senha_hash" class="form-label">Senha</label>
                    <input class="card-secundario" type="password" id="senha_hash" name="senha_hash" value="<?= isset($funcionario['senha_hash']) ? htmlspecialchars($funcionario['senha_hash']) : '' ?>">
                    <?php if (isset($erros['senha_hash'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['senha_hash']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="numero_telefone" class="form-label">Numero de telefone</label>
                    <input class="card-secundario" type="number" id="numero_telefone" name="numero_telefone" value="<?= isset($funcionario['numero_telefone']) ? htmlspecialchars($funcionario['numero_telefone']) : '' ?>">
                    <?php if (isset($erros['numero_telefone'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['numero_telefone']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="formgroup">
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <select class="card-secundario" id="id_empresa" name="id_empresa">
                        <option value="" hidden disabled <?= !isset($empresa['id_empresa']) ? 'selected' : '' ?>>Selecione</option>
                        <?php foreach($empresas as $empresa):?>
                            <option value="<?= $empresa['id_empresa'] ?>"><?= $empresa['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="botao brilho">Salvar</button>
            </form>
        
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cnpjInput = document.getElementById('cnpj');
            if (!cnpjInput) return;

            const formatCnpj = (value) => {
                const digits = (value || '').replace(/\D/g, '').slice(0, 14);
                if (!digits) return '';
                return digits.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
            };

            cnpjInput.value = formatCnpj(cnpjInput.value);

            cnpjInput.addEventListener('input', function () {
                cnpjInput.value = formatCnpj(cnpjInput.value);
            });

            cnpjInput.closest('form').addEventListener('submit', function () {
                cnpjInput.value = cnpjInput.value.replace(/\D/g, '');
            });
        });
    </script>
    
</body>
</html>
