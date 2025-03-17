
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);
INSERT INTO usuarios (nome, email, senha) 
VALUES ('Admin', 'admin@email.com', SHA2('123456', 256));