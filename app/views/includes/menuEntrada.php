<nav id="menu-entrada" aria-label="Navegação principal">
    <!--volta para a pagina inicial-->
    <a id="homepage" href="<?= URL_BASE ?>" aria-label="Ir para a página inicial">
        <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="">
        <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="LudoSkill">
    </a>

    <!--decide o tema do site-->
    <div id="controle-tema">
        <label for="seletor-tema">Tema</label>
        <select name="tema" id="seletor-tema" class="seletor">
            <option hidden disabled selected value="padrao">Padrão</option>
            <option value="escuro">Escuro</option>
            <option value="claro">Claro</option>
        </select>
    </div>

    <!--botões para outras páginas-->
    <ul id="navegacao">
        <li><a href="<?= URL_BASE ?>/sobreNos">Sobre nós</a></li>
        <li><a class="botao brilho" href="<?= URL_BASE ?>/login">Login</a></li>
    </ul>
</nav>
