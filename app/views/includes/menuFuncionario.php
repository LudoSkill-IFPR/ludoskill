<nav class="menu-funcionario">
    <a id="homepage" href="<?= URL_BASE ?>/funcionario/inicial">
        <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="Ícone do LudoSkill">
        <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="LudoSkill">
    </a>

    <ul id="navegacao">
        <li><a href="<?= URL_BASE ?>/funcionario/modulos">Módulos</a></li>
        <li class="dropdown">
            <a class="botao brilho" href="#"><i class="bi bi-person-circle"></i> Perfil</a>

            <div class="dropdown-menu">
                <a href="<?= URL_BASE ?>/logout"><i class="bi bi-door-open-fill"></i> Logout</a>
            </div>
        </li>
        
    </ul>
</nav>
