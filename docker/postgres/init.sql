CREATE TABLE clientes (
    id INTEGER PRIMARY KEY,
    nome varchar(150) not null,
    limite INTEGER NOT NULL,
    saldo INTEGER NOT NULL
);

CREATE TABLE transacoes (
    id BIGSERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL,
    valor INTEGER NOT NULL,
    tipo CHAR(1) NOT NULL,
    descricao VARCHAR(10) NOT NULL,
    realizada_em TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_transacoes_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
);

CREATE INDEX idx_transacoes_cliente_data
ON transacoes (
    cliente_id,
    realizada_em DESC,
    id DESC
);

--- insert

INSERT INTO clientes (id, nome, limite, saldo)
  VALUES
    (1, 'o barato sai caro', 1000 * 100, 0),
    (2, 'zan corp ltda', 800 * 100, 0),
    (3, 'les cruders', 10000 * 100, 0),
    (4, 'padaria joia de cocaia', 100000 * 100, 0),
    (5, 'kid mais', 5000 * 100, 0);