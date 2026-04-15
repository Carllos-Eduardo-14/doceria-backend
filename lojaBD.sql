-- CLIENTE
CREATE TABLE cliente (
    idCliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    senha VARCHAR(255) NOT NULL,
    dataCadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIA
CREATE TABLE categoria (
    idCategoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- PRODUTO
CREATE TABLE produto (
    idProduto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT DEFAULT 0,
    idCategoria INT,
    FOREIGN KEY (idCategoria) REFERENCES categoria(idCategoria)
);

-- FAVORITOS
CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idCliente INT,
    idProduto INT,
    UNIQUE (idCliente, idProduto),
    FOREIGN KEY (idCliente) REFERENCES cliente(idCliente) ON DELETE CASCADE,
    FOREIGN KEY (idProduto) REFERENCES produto(idProduto) ON DELETE CASCADE
);

-- PEDIDO
CREATE TABLE pedido (
    idPedido INT AUTO_INCREMENT PRIMARY KEY,
    idCliente INT,
    dataPedido DATETIME,
    dataEntrega DATE,
    horaEntrega TIME,
    status VARCHAR(50) DEFAULT 'PENDENTE',
    valorTotal DECIMAL(10,2),
    observacao TEXT,
    tipoEntrega ENUM('RETIRADA','DELIVERY'),
    enderecoEntrega TEXT,
    taxaEntrega DECIMAL(10,2),
    metodoPagamento VARCHAR(50),
    FOREIGN KEY (idCliente) REFERENCES cliente(idCliente)
);

-- ITENS DO PEDIDO
CREATE TABLE itemPedido (
    idItem INT AUTO_INCREMENT PRIMARY KEY,
    idPedido INT,
    idProduto INT,
    quantidade INT,
    subtotal DECIMAL(10,2),
    FOREIGN KEY (idPedido) REFERENCES pedido(idPedido) ON DELETE CASCADE,
    FOREIGN KEY (idProduto) REFERENCES produto(idProduto)
);

-- AVALIAÇÃO
CREATE TABLE avaliacao (
    idAvaliacao INT AUTO_INCREMENT PRIMARY KEY,
    idProduto INT,
    idCliente INT,
    nota INT CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    dataAvaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idProduto) REFERENCES produto(idProduto) ON DELETE CASCADE,
    FOREIGN KEY (idCliente) REFERENCES cliente(idCliente)
);

-- PAGAMENTO (OPCIONAL)
CREATE TABLE pagamento (
    idPagamento INT AUTO_INCREMENT PRIMARY KEY,
    idPedido INT,
    metodo VARCHAR(50),
    status VARCHAR(50),
    valor DECIMAL(10,2),
    dataPagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idPedido) REFERENCES pedido(idPedido) ON DELETE CASCADE
);