CREATE DATABASE salaofy;

USE salaofy;

CREATE TABLE perfis (
    id_perfil INT PRIMARY KEY,
    nome_perfil VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    nome_completo VARCHAR(100) NOT NULL,
    nome_social_apelido VARCHAR(50),
    contato_celular VARCHAR(15) NOT NULL,
    id_perfil INT NOT NULL,
    FOREIGN KEY (id_perfil) REFERENCES perfis(id_perfil)
);

CREATE TABLE condominios (
    id_condominio INT PRIMARY KEY AUTO_INCREMENT,
    nome_condominio VARCHAR(100) NOT NULL,
    cep VARCHAR(8) NOT NULL,
    id_sindico INT UNIQUE,
    FOREIGN KEY (id_sindico) REFERENCES usuarios(id_usuario)
);

CREATE TABLE saloes (
    id_salao INT PRIMARY KEY AUTO_INCREMENT,
    nome_salao VARCHAR(100) NOT NULL,
    regras_uso TEXT,
    preco_locacao_base DECIMAL(10, 2),
    capacidade_maxima INT,
    id_condominio INT NOT NULL,
    FOREIGN KEY (id_condominio) REFERENCES condominios(id_condominio)
);

CREATE TABLE morador_condominio (
    id_vinculo INT PRIMARY KEY AUTO_INCREMENT,
    id_morador INT NOT NULL,
    id_condominio INT NOT NULL,
    FOREIGN KEY (id_morador) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_condominio) REFERENCES condominios(id_condominio),
    UNIQUE (id_morador, id_condominio)
);

CREATE TABLE agendamentos (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    id_salao INT NOT NULL,
    id_morador INT NOT NULL,
    data_evento DATE NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pendente', 'Confirmada', 'Rejeitada', 'Cancelada') NOT NULL DEFAULT 'Pendente',
    valor_total DECIMAL(10, 2),
    detalhes_evento VARCHAR(255),
    
    FOREIGN KEY (id_salao) REFERENCES saloes(id_salao),
    FOREIGN KEY (id_morador) REFERENCES usuarios(id_usuario),
    
    UNIQUE (id_salao, data_evento)
);

INSERT INTO perfis (id_perfil, nome_perfil) VALUES (1, 'Admin'), (2, 'Sindico'), (3, 'Morador');
