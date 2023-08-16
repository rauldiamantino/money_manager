## Money Manager

Após criar sua conta e realizar o login, você pode adicionar receitas e despesas, além de organizá-las por contas e categorias.

## Requisitos e Instalação:

- É muito importante que você já possua um ambiente PHP e MySQL configurado, caso não tenha, recomendo que instale o MAMP ou XAMPP.
- Clone este repositório no diretório padrão do seu localhost.
- Após realizar a configuração do ambiente, crie o database principal e a tabela users.
- Depois de ter feito os passos anteriores, acesse a pasta `public` através do localhost no navegador.

### Criando o database principal e tabela users

Database:
 ```
 host: localhost
 database name: estudo_mvc
 user: root
 password: root
 ```

Tabela users
 ```
 CREATE TABLE users (
   id INT AUTO_INCREMENT PRIMARY KEY,
   first_name VARCHAR(100) NOT NULL,
   last_name VARCHAR(100) NOT NULL,
   email VARCHAR(100) NOT NULL,
   password VARCHAR(255) NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 );
```
