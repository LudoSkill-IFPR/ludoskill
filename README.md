<p align="center">
  <img src="public/assets/imagens/logo-ludoskill.png" alt="LudoSkill" width="360">
</p>

<h3 align="center">Sistema gamificado de treinamento básico na área de assistência técnica móvel</h3>

<p align="center">
  Projeto Integrador · Técnico em Desenvolvimento de Sistemas · IFPR Campus Foz do Iguaçu · 2026
</p>

<p align="center">
  <a href="https://ludoskill.alwaysdata.net"><strong>Acessar o sistema</strong></a>
</p>

---

## Sobre o projeto

O **LudoSkill** é uma plataforma digital que capacita novos técnicos de assistência a dispositivos móveis por meio da **gamificação** e de **simulações práticas**. Jovens recém-formados costumam esbarrar na exigência de experiência para conseguir o primeiro emprego, e o treinamento tradicional é caro e difícil de escalar. O LudoSkill oferece um curso em módulos, com atividades teóricas, revisões e simulações, e dá aos gestores das empresas ferramentas para acompanhar o desempenho de cada funcionário.

A fundamentação pedagógica se apoia na Teoria Cognitiva da Aprendizagem Multimídia (Mayer), nas pesquisas sobre simulações de Chernikova et al. e na framework de gamificação **Octalysis** (Yu-kai Chou).

## Funcionalidades

| Perfil | O que pode fazer |
|---|---|
| **Visitante** | Conhecer a plataforma, ler a página "Sobre nós" e solicitar acesso para a sua empresa |
| **Funcionário** | Fazer as atividades dos módulos (introdução, revisão, texto e simulação), ganhar estrelas e bolotas, acompanhar o próprio progresso, comprar e equipar itens na loja e escolher o tema do sistema |
| **Gestor** | Cadastrar, editar, inativar e reativar os funcionários da sua empresa e acompanhar o desempenho deles em um gráfico |
| **Administrador** | Manter empresas, gestores, módulos, atividades, exercícios e itens da loja, aprovar solicitações de acesso e ver o relatório de usuários por empresa |

### Regras de gamificação

- ⭐ **Estrelas:** cada atividade concluída rende estrelas. Cada erro desconta 1 estrela, e passar do tempo limite desconta mais 1.
- ✅ **Conclusão:** a atividade só é concluída com pelo menos **66% de acertos**.
- 🔁 **Repetição:** as atividades podem ser refeitas sem limite, e fica registrada apenas a **maior pontuação**.
- 🔓 **Progressão:** as atividades são liberadas em sequência, e cada módulo exige um mínimo de estrelas para ser desbloqueado.
- 🌰 **Bolotas:** a moeda virtual do sistema. Elas são ganhas uma única vez por atividade e gastas na loja em **temas** (cores do site) e **visuais esquilosos** (aparência do mascote).

## Tecnologias

| Camada | Ferramentas |
|---|---|
| Front-end | HTML5, CSS3, JavaScript, Bootstrap, Bootstrap Icons, Chart.js, TinyMCE |
| Back-end | PHP 8 (MVC próprio, sem framework), PHPMailer |
| Banco de dados | MySQL / MariaDB |
| Servidor | Apache com `mod_rewrite` |
| Hospedagem | Alwaysdata, com deploy automático via GitHub Actions |
| Planejamento | Penpot (protótipos), LucidChart (diagramas), Krita (ilustrações), Trello |

## Estrutura de pastas

```
ludoskill/
├── .github/workflows/deploy.yml   # deploy automático no Alwaysdata
├── .htaccess                      # redireciona tudo para public/
├── app/
│   ├── bibliotecas/PHPMailer/     # envio de e-mails
│   ├── config/Config.php          # constantes e leitura das variáveis de ambiente
│   ├── controllers/               # recebem as requisições
│   ├── core/                      # Router, Controller base e Autoload
│   ├── database/
│   │   ├── banco.sql              # estrutura completa do banco
│   │   └── updates/               # scripts para atualizar bancos antigos
│   ├── helpers/                   # validações, e-mail, TinyMCE
│   ├── models/                    # entidades do sistema
│   ├── repositories/              # acesso ao banco (PDO)
│   ├── services/                  # regras de negócio
│   └── views/                     # páginas (entrada, funcionário, gestor, administrador)
├── public/                        # raiz pública do site
│   ├── index.php                  # rotas da aplicação
│   └── assets/                    # CSS, JS, imagens e uploads
└── tests/                         # testes executados pelo terminal
```

## Como rodar localmente

### Pré-requisitos

- PHP **8.0 ou superior**, com a extensão `pdo_mysql`
- MySQL ou MariaDB
- Apache com `mod_rewrite` ativo (o XAMPP já traz tudo isso)

### Passo a passo

1. **Clone o repositório** dentro da pasta do servidor (no XAMPP, `htdocs`):
   ```bash
   git clone https://github.com/LudoSkill-IFPR/ludoskill.git
   ```

2. **Configure as variáveis de ambiente.** O sistema lê toda a configuração com `getenv()`. No Apache, uma forma simples é acrescentar as linhas abaixo ao `httpd.conf` (ou ao VirtualHost do projeto) e reiniciar o Apache:
   ```apache
   SetEnv APP_ENV development
   SetEnv DB_HOST localhost
   SetEnv DB_NAME ludoskill
   SetEnv DB_USER root
   SetEnv DB_PASS ""
   SetEnv LUDOSKILL_ADMIN_EMAIL admin@ludoskill.com
   SetEnv LUDOSKILL_ADMIN_PASSWORD SuaSenhaForte123
   SetEnv LUDOSKILL_ADMIN_CPF 00000000191
   ```
   > ⚠️ Não coloque senhas reais em arquivos versionados no Git.

3. **Crie o banco.** Na primeira conexão, se o banco informado em `DB_NAME` ainda não existir, o sistema o cria sozinho a partir do `app/database/banco.sql` e cadastra o administrador inicial com os dados das variáveis `LUDOSKILL_ADMIN_*`. Se preferir, importe o `banco.sql` manualmente pelo phpMyAdmin.

3.1 **Para a banca.** A criação do banco deve acompanhar o arquivo "SQLBanca.sql". A partir dele serão criados usuários para acessar o sistema entre os demais tipos: funcionário, gestor e administrador.

4. **Acesse** `http://localhost/ludoskill/` e entre com o e-mail e a senha do administrador.

### Variáveis de ambiente

| Variável | Obrigatória | Descrição |
|---|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | ✅ | Conexão com o banco de dados |
| `APP_ENV` | | `development` mostra os erros na tela; em produção, deixe `production` |
| `APP_URL` | | URL pública do site (ex.: `https://ludoskill.alwaysdata.net`). Sem ela, a URL é detectada automaticamente |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION` | | Servidor SMTP (padrão: `smtp.gmail.com`, porta `587`, TLS) |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | Para e-mails | Conta que envia os e-mails. No Gmail, use uma **senha de app** |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | | Remetente exibido nos e-mails |
| `LUDOSKILL_ADMIN_EMAIL`, `LUDOSKILL_ADMIN_PASSWORD`, `LUDOSKILL_ADMIN_CPF` | Na 1ª execução | Administrador criado junto com o banco |
| `LUDOSKILL_ADMIN_NAME`, `LUDOSKILL_ADMIN_BIRTHDATE`, `LUDOSKILL_ADMIN_PHONE` | | Dados opcionais do administrador inicial |

Os e-mails são usados na recuperação de senha e na resposta às solicitações de acesso.

## Deploy

O sistema está hospedado no **Alwaysdata**, com a raiz do site apontando para a pasta `public/` e as variáveis de ambiente cadastradas no painel.

A cada `push` na branch `main`, o workflow `.github/workflows/deploy.yml` entra no servidor por SSH e executa `git pull`, atualizando o site automaticamente. O workflow usa três *secrets* do repositório: `SSH_HOST`, `SSH_USER` e `SSH_PASSWORD`.

> Toda alteração deve passar pelo GitHub. Arquivos editados direto no servidor fazem o `git pull` falhar.

## Testes

Os testes ficam na pasta `tests/` e são executados pelo terminal:

```bash
php tests/validacoes_cadastrais.php
php tests/smoke_views.php
```

## Equipe

| Integrantes | Orientadores |
|---|---|
| Alinne Helena Estigarribia dos Santos | Jefferson de Oliveira Chaves |
| Izabella da Costa Codo | Ana Carla Arruda de Holanda |
| Rebeca Schmidt | |
| Vinícius Brevesteky Teixeira | |

---

<p align="center">
  Projeto acadêmico desenvolvido no <strong>Instituto Federal do Paraná, Campus Foz do Iguaçu</strong> · 2026 🐿️
</p>
