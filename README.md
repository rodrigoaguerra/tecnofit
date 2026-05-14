# 🛠️ Tecnofit API de Ranking de Movimentos

Este projeto consiste em uma API RESTful desenvolvida em PHP puro, utilizando PDO para acesso ao banco de dados MySQL.

A aplicação tem como objetivo fornecer o ranking de um determinado movimento com base nos dados armazenados no banco, retornando as informações em formato JSON.

## 🔗 Endpoint's disponível
- GET /ranking/{id_ou_nome}
- GET /ranking?id={id}
- GET /ranking?name={nome}

Retorna o ranking de um movimento específico, podendo ser consultado pelo ID ou nome.

## 🚀 Tecnologias Utilizadas
- Apache 2 (servidor http)
- PHP 8.4.20 (sem frameworks)
- PDO (PHP Data Objects)
- MySQL 8.0.45
- Composer (autoload e organização do projeto)

## 📌 1. Instalação do Projeto
Siga os passos abaixo para instalar e configurar a api.

### 🔹 1.1 Clonar o Repositório
```sh
git clone https://github.com/rodrigoaguerra/tecnofit.git
```
### 🔹 1.2 Navegar para pasta do projeto
```sh
cd tecnofit
```

### 🔹 1.3 Instalar Dependências
```sh
composer install
```

### 🔹 1.4 Configurar o Banco de Dados
1. **Crie um banco de dados no MySQL**
2. **Copie o arquivo de configuração**
```sh
cp config-example.php config.php
```
3. **Edite o arquivo** 'config.php' e configure a conexão com o banco:
```php
// Configurações da aplicação
define( 'APP_NAME', 'NOME_DA_APLICAÇÃO' );
define( 'APP_ENV', 'development' );
define( 'METHODS_ALLOWED', ['GET', 'OPTIONS'] ); // Métodos HTTP permitidos ex: 'PUT', 'PATCH', 'DELETE'

define( 'ALLOWED_ORIGINS', [
  'http://localhost:8000',
  'http://localhost:8080', 
  'http://127.0.0.1:8000', 
  'http://127.0.0.1:8080'  
]); // preencha o array com urls validas para a api

// Configuração do banco de dados
define( 'DB_HOST', 'ENDEREÇO_DO_HOST' );
define( 'DB_PORT', 'PORTA_DO_BANCO_MYSQL' );
define( 'DB_NAME', 'NOME_DO_BANCO_CRIADO' );
define( 'DB_USER', 'USUARIO_DO_BANCO' );
define( 'DB_PASSWORD', 'SENHA_DO_BANCO' );
```

### 🔹 1.5 importar o banco de dados
```sh
composer db:import
```

### 🔹 1.6 Iniciar o Servidor
```sh
composer start
```
ou 
```sh
php -S localhost:8000 -t public
```
A api estará disponível em: http://localhost:8000

## 📌 2. Testar o endpoint da api
exemplos de uso:
  - GET http://localhost:8000/ranking/1?page=1&limit=10
  - GET http://localhost:8000/ranking?id=2&page=1&limit=10
  - GET http://localhost:8000/ranking/Deadlift?page=1&limit=10
  - GET http://localhost:8000/ranking?name=Deadlift&page=1&limit=10

## 📌 3. Funcionalidades
- ✅ Buscar ranking de movimento por id ou nome

## 📌 4. Créditos
Desenvolvido por [**Rodrigo Alves Guerra 🖥️🚀**](https://rodrigoalvesguerra.com.br)

## 📌 5. Demo 
  - [api em produção](https://api-tecnofit.rodrigoalvesguerra.com.br)
  - [front-end aplicação](https://tecnofit.rodrigoalvesguerra.com.br)
