CREATE DATABASE sistema_reservas;
USE sistema_reservas;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_usu VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    data_nas date not null,
    estado varchar(50) not null,
    cidade varchar(100) not null,
    cep varchar(8) not null,
    cpf VARCHAR(20) NOT NULL,
    endereco VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    genero enum('F', 'M') not null
);

CREATE TABLE administrador (
    id_administrador INT AUTO_INCREMENT PRIMARY KEY,
    nome_adm VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE estabelecimento (
    id_estabelecimento INT AUTO_INCREMENT PRIMARY KEY,
    nome_est VARCHAR(100) NOT NULL,
    tipo ENUM('Vôlei','Futebol','Basquete','Piscina', 'Poliesportivo','Outros') NOT NULL,
    endereco VARCHAR(150) NOT NULL,
    numero NUMERIC(5) NOT NULL,
    bairro VARCHAR(150) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    cidade VARCHAR(150) NOT NULL,
    complemento VARCHAR(150),
    uf VARCHAR(2) NOT NULL,
    status ENUM('Ativo', 'Inativo') NOT NULL,
    inicio TIME NOT NULL,
    termino TIME NOT NULL,
    disponibilidade ENUM('Seg-Sex', 'Sab-Dom', 'Seg-Dom') NOT NULL,
    id_administrador INT NOT NULL,
    FOREIGN KEY (id_administrador) REFERENCES administrador(id_administrador)
);

select * from estabelecimento;
CREATE TABLE espaco (
    id_espaco INT AUTO_INCREMENT PRIMARY KEY,
    capacidade INT NOT NULL,
    cobertura ENUM('Sim', 'Não') NOT NULL,
    largura VARCHAR(10),
    comprimento VARCHAR(10),
    descricao_adicional VARCHAR(10000) NOT NULL,
    localidade VARCHAR(100) NOT NULL,
    status ENUM('disponível', 'indisponível') NOT NULL,
    id_estabelecimento INT NOT NULL,
    FOREIGN KEY (id_estabelecimento) REFERENCES estabelecimento(id_estabelecimento)
);


CREATE TABLE reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    status ENUM('pendente', 'concluída', 'cancelada') NOT NULL,
    capacidade INT NOT NULL, 
	data_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    id_estabelecimento INT NOT NULL,
    id_espaco INT NOT NULL,
    id_administrador INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_estabelecimento) REFERENCES estabelecimento(id_estabelecimento),
    FOREIGN KEY (id_espaco) REFERENCES espaco(id_espaco),
    FOREIGN KEY (id_administrador) REFERENCES administrador(id_administrador)
);

CREATE TABLE instalacao_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_estabelecimento INT NOT NULL,
    caminho_foto VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO instalacao_fotos (id_estabelecimento, caminho_foto)
VALUES 
(1, 'uploads_instalacoes/espaco1_foto1.jpg'),
(1, 'uploads_instalacoes/espaco1_foto2.jpg'),
(2, 'uploads_instalacoes/espaco2_foto1.png');

INSERT INTO administrador (nome_adm, email, telefone, endereco, senha) VALUES
('Ana Souza', 'ana@admin.com', '11987654321', 'Rua das Flores, 100',MD5('12345')),
('Carlos Pereira', 'carlos@admin.com', '11999887766', 'Av. Central, 200', MD5('12345')),
('Marcos Lima', 'marcos@admin.com', '11911223344', 'Rua Azul, 50', MD5('12345')),
('Patrícia Gomes', 'patricia@admin.com', '11922334455', 'Av. Verde, 75', MD5('12345')),
('Renato Alves', 'renato@admin.com', '11933445566', 'Rua Vermelha, 33', MD5('12345')),
('Juliana Costa', 'juliana@admin.com', '11944556677', 'Av. Laranja, 88', MD5('12345')),
('Eduardo Santos', 'eduardo@admin.com', '11955667788', 'Rua Amarela, 101', MD5('12345')),
('Fernanda Melo', 'fernanda@admin.com', '11966778899', 'Av. Roxa, 120', MD5('12345')),
('Lucas Ribeiro', 'lucas@admin.com', '11977889900', 'Rua Rosa, 200', MD5('12345')),
('Carla Martins', 'carla@admin.com', '11988990011', 'Av. Prata, 250', MD5('12345'));


INSERT INTO usuario 
(nome_usu, email, telefone, data_nas, estado, cidade, cep, cpf, endereco, senha, genero)
VALUES
('Ana Pereira', 'ana.pereira@example.com', '11987654321', '1995-03-12', 'São Paulo', 'São Paulo', '01001000', '12345678901', 'Rua das Flores, 120', MD5('12345'), 'F'),

('Marcos Silva', 'marcos.silva@example.com', '21999887766', '1988-07-25', 'Rio de Janeiro', 'Rio de Janeiro', '20040002', '98765432100', 'Av. Atlântica, 450', MD5('12345'), 'M'),

('Juliana Costa', 'juliana.costa@example.com', '31988776655', '2000-11-09', 'Minas Gerais', 'Belo Horizonte', '30140071', '45678912300', 'Rua Goiás, 89', MD5('12345'), 'F'),

('Pedro Almeida', 'pedro.almeida@example.com', '41977665544', '1992-02-18', 'Paraná', 'Curitiba', '80010020', '65498732100', 'Av. Sete de Setembro, 980', MD5('12345'), 'M'),

('Carla Menezes', 'carla.menezes@example.com', '61966554433', '1999-05-03', 'Distrito Federal', 'Brasília', '70040900', '85236974100', 'SQN 205, Bloco C', MD5('12345'), 'F');



INSERT INTO estabelecimento
(nome_est, tipo, endereco, numero, bairro, cep, cidade, complemento, uf, status, inicio, termino, disponibilidade, id_administrador)
VALUES
('Arena Paulista', 'Futebol', 'Rua Alfa', 100, 'Centro', '01010000', 'São Paulo', 'Próx. metrô', 'SP', 'Ativo', '08:00', '22:00', 'Seg-Sex', 1),
('Quadra Rio Sport', 'Vôlei', 'Rua Beta', 200, 'Zona Sul', '22020000', 'Rio de Janeiro', 'Ao lado do parque', 'RJ', 'Ativo', '07:00', '23:00', 'Seg-Dom', 2),
('Ginásio Minas Arena', 'Basquete', 'Rua Gama', 300, 'Centro', '30130000', 'Belo Horizonte', 'Entrada A', 'MG', 'Ativo', '09:00', '21:00', 'Seg-Sex', 3),
('Complexo Curitiba', 'Piscina', 'Rua Delta', 400, 'Batel', '80440000', 'Curitiba', 'Portão 3', 'PR', 'Inativo', '06:00', '20:00', 'Seg-Dom', 4),
('Parque Sul Esportes', 'Poliesportivo', 'Rua Épsilon', 500, 'Sul', '90050000', 'Porto Alegre', 'Quadra 2', 'RS', 'Ativo', '07:00', '22:00', 'Seg-Sex', 5),
('Centro Atlântico', 'Outros', 'Rua Zeta', 600, 'Beira Mar', '88060000', 'Florianópolis', NULL, 'SC', 'Ativo', '08:00', '20:00', 'Sab-Dom', 6),
('Arena Bahia Pro', 'Futebol', 'Rua Eta', 700, 'Comércio', '40070000', 'Salvador', 'Prédio Azul', 'BA', 'Ativo', '09:00', '23:00', 'Seg-Dom', 7),
('Quadra Recife Top', 'Vôlei', 'Rua Teta', 800, 'Boa Viagem', '50080000', 'Recife', NULL, 'PE', 'Inativo', '06:00', '19:00', 'Seg-Sex', 8),
('Estádio Goiás Center', 'Futebol', 'Rua Iota', 900, 'Centro', '74090000', 'Goiânia', 'Ao lado do estádio', 'GO', 'Ativo', '07:00', '21:00', 'Seg-Dom', 9),
('Multi Esportes Campinas', 'Poliesportivo', 'Rua Kappa', 1000, 'Cambuí', '13010000', 'Campinas', NULL, 'SP', 'Ativo', '08:00', '22:00', 'Sab-Dom', 10);

INSERT INTO espaco 
(capacidade, cobertura, largura, comprimento, descricao_adicional, localidade, status, id_estabelecimento)
VALUES
( 12, 'Sim', '9m', '18m', 'Quadra oficial com piso emborrachado', 'Bloco A', 'disponível', 1),
( 22, 'Não', '45m', '90m', 'Campo gramado padrão amador', 'Campo 1', 'disponível', 2),
( 10, 'Sim', '15m', '28m', 'Quadra com tabela profissional e piso polido', 'Ginásio 1', 'indisponível', 3),
( 30, 'Não', '25m', '50m', 'Piscina semiolímpica com 6 raias', 'Área Aquática', 'disponível', 4),
( 25, 'Sim', '20m', '40m', 'Espaço multiuso para diversas modalidades', 'Ginásio 2', 'disponível', 5),
( 40, 'Sim', '10m', '15m', 'Área destinada a atividades gerais e eventos', 'Sala Multiuso', 'disponível', 6),
( 18, 'Não', '30m', '60m', 'Campo society com grama sintética', 'Campo 2', 'disponível', 7);

INSERT INTO reserva 
(data, horario_inicio, horario_fim, status, capacidade, id_usuario, id_estabelecimento, id_espaco, id_administrador)
VALUES
('2025-01-10', '08:00:00', '09:00:00', 'pendente', 12, 1, 1, 1, 1),

('2025-01-12', '09:00:00', '10:30:00', 'concluída', 22, 2, 2, 2, 1),

('2025-01-15', '14:00:00', '15:00:00', 'pendente', 10, 3, 3, 3, 2),

('2025-01-20', '18:00:00', '19:30:00', 'cancelada', 30, 5, 4, 4, 1),

('2025-01-22', '07:30:00', '09:00:00', 'pendente', 18, 4, 5, 5, 2);

CREATE TABLE documentos (
    id_usuario INT PRIMARY KEY,
    rg_foto VARCHAR(255),
    endereco_foto VARCHAR(255),
    selfie_rg VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

INSERT INTO documentos (id_usuario, rg_foto, endereco_foto, selfie_rg)
VALUES
(1, 'uploads/rg_1.jpg', 'uploads/endereco_1.jpg', 'uploads/selfie_1.jpg'),
(2, 'uploads/rg_2.png', 'uploads/endereco_2.png', 'uploads/selfie_2.png'),
(3, 'docs/rg_3.jpeg', 'docs/endereco_3.jpeg', 'docs/selfie_3.jpeg');