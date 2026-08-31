<nav>
    <!--volta para a pagina inicial-->
    <a id="homepage" href="<?= URL_BASE ?>">
        <img id="icone" src="assets/imagens/icone-ludoskill-transparente.png" alt="icone do ludoskill">
        <img id="logo" src="assets/imagens/logo-ludoskill.png" alt="logo do ludoskill">
    </a>

    <!--decide o tema do site-->
    <div id="temas">
        <label for="temas">Temas</label>
        <select name="temas" id="temas" class="seletor">
            <option hidden disabled value="padrao">Padrão</option>
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