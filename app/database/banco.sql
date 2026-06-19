create table Usuario(
    id_usuario auto_increment primary key,
    nome_completo varchar(255) not null,
    data_nascimento date not null,
    cpf char(11) not null unique,
    email varchar(255) not null unique,
    senha char(60) not null,
    numero_telefone char(20) not null unique,
    perfil char(1) not null,
    constraint check_perfil check (perfil in ('F', 'G', 'A')) --funcionario, gestor e administrador
);

create table if not exists Empresa(
    id_empresa auto_increment primary key,
    nome varchar(255) not null,
    cnpj char(14) not null unique,
    numero_funcionarios int not null,
    email varchar(255) not null,
    plano char(1) not null,
    constraint check_plano check (plano in ('B', 'P')) --basico e premium
);

create table if not exists Gestor(
    id_usuario int not null,
    id_empresa int not null,
    id_gestor auto_increment primary key,
    foreign key (id_usuario) references Usuario(id_usuario);
    foreign key (id_empresa) references Empresa(id_empresa);
);

create table if not exists Administrador(
    id_usuario int not null,
    id_administrador auto_increment primary key,
    foreign key (id_usuario) references Usuario(id_usuario);
);

create table if not exists Modulo(
    id_modulo auto_increment primary key,
    nome varchar(255) not null,
    descricao varchar(255) not null,
    min_estrelas_liberacao int(3) not null
);

create table if not exists Fase(
    id_fase auto_increment primary key,
    id_modulo int not null,
    tipo_fase char(1) not null,
    nome varchar(255) not null,
    estado bit not null, --1 para concluído e 0 para não iniciado
    estrelas_obtidas int(3) not null, 
    tarefa json not null,
    foreign key (id_modulo) references Modulo(id_modulo);
    constraint check_tipo_fase check (tipo_fase in ('I', 'S', 'R')) --introdução, simulação e revisão
    -- constraint check_estado check (estado in ('N', 'C')) --Não iniciado e concluído
);

create table if not exists Progresso(
    id_progresso auto_increment primary key,
    id_funcionario int not null,
    id_fase int not null,
    id_modulo int not null,
    estrelas_totais int(3) not null,
    foreign key (id_funcionario) references Funcionario(id_funcionario);
    foreign key (id_fase) references Fase(id_fase);
    foreign key (id_modulo) references Modulo(id_modulo);
);

create table if not exists Inventario(
    id_inventario auto_increment primary key,
    id_funcionario int not null,
    foreign key (id_funcionario) references Funcionario(id_funcionario);
);

create table if not exists Item(
    id_item auto_increment primary key,
    nome varchar(255) not null,
    tipo char(1) not null,
    estado bit not null, --1 para ativo e 0 para inativo
    preco int(3) not null,
    imagem varchar(255) not null,
    constraint check_tipo check (tipo in ('B', 'P')) --bolota e power-up
);

create table if not exists Funcionario(
    id_funcionario auto_increment primary key,
    id_usuario int not null,
    id_progresso int not null,
    id_inventario int not null,
    bolotas int(7) not null,
    desempenho int(3) not null,
    foreign key (id_usuario) references Usuario(id_usuario);
    foreign key (id_progresso) references Progresso(id_progresso);
    foreign key (id_inventario) references Inventario(id_inventario);
);

