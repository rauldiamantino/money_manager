CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE SCHEMA money_manager_user_1;

USE money_manager_use_1;

CREATE TABLE categories (
	id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE accounts (
	id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE expenses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  description VARCHAR(255) NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  category_id INT,
  account_id INT,
  date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (account_id) REFERENCES accounts(id)
);

CREATE TABLE incomes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  description VARCHAR(255) NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  category_id INT,
  account_id INT,
  date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (account_id) REFERENCES accounts(id)
);

-- Insere os dados na tabela users
INSERT INTO users (name, email, password) VALUES
('João Silva', 'joao@example.com', 'senha123'),
('Maria Santos', 'maria@example.com', 'senha456'),
('Pedro Oliveira', 'pedro@example.com', 'senha789');

-- informações específicas para o usuário 1

INSERT INTO categories (name) VALUES
('Lazer'), ('Saúde'), ('Educação');

INSERT INTO accounts (name) VALUES
('Conta Corrente'), ('Investimentos');

INSERT INTO expenses (description, amount, category_id, account_id, date)
VALUES ('Cinema', 40.00, 1, 1, '2023-08-02'),
       ('Médico', 100.00, 2, 2, '2023-08-04');

INSERT INTO incomes (description, amount, category_id, account_id, date)
VALUES ('Salário', 3000.00, 3, 1, '2023-08-05'),
       ('Freelance', 800.00, 3, 1, '2023-08-12');

-- informações específicas para o usuário 2
INSERT INTO categories (name) VALUES
('Compras'), ('Viagens'), ('Investimentos');

INSERT INTO accounts (name) VALUES
('Conta Corrente'), ('Cartão de Crédito');

INSERT INTO expenses (description, amount, category_id, account_id, date)
VALUES ('Roupas', 200.00, 1, 1, '2023-08-05'),
       ('Passagem Aérea', 500.00, 2, 2, '2023-08-08');

INSERT INTO incomes (description, amount, category_id, account_id, date)
VALUES ('Salário', 1800.00, 3, 1, '2023-08-01'),
       ('Investimento', 1000.00, 3, 1, '2023-08-15');

-- informações específicas para o usuário 3

INSERT INTO categories (name) VALUES
('Alimentação'), ('Transporte'), ('Moradia');

INSERT INTO accounts (name) VALUES
('Conta Corrente'), ('Poupança');

INSERT INTO expenses (description, amount, category_id, account_id, date)
VALUES ('Compras no mercado', 150.00, 1, 1, '2023-08-01'),
       ('Transporte público', 30.00, 2, 2, '2023-08-03');

INSERT INTO incomes (description, amount, category_id, account_id, date)
VALUES ('Salário', 2500.00, 3, 1, '2023-08-05'),
       ('Renda Extra', 500.00, 3, 1, '2023-08-10');