# REST API em PHP utilizando Laravel e MySQL

Esta API permite a inserção de clientes no banco de dados, realizando validações de email, endereço, CPF/CNPJ, entre outras.

## Instalação

Para executar esta API, primeiramente é necessário instalar o PHP e o Composer. Neste projeto, foi utilizado o XAMPP, que pode ser baixado a partir do site https://www.apachefriends.org/download.html. Para baixar o Composer, acesse https://getcomposer.org/download/.

Com o XAMPP instalado, clone o repositório dentro de xampp/htdocs. Inicie o servidor Apache e MySQL.

Dentro da pasta raiz do projeto, instale as dependências:

```
composer install
```

Para esta implementação, os dados do arquivo .env são os mesmos do arquivo .env.example, para fins de demonstração. Crie um arquivo .env na raiz do projeto, copie os dados do arquivo .env.example e cole no .env. Altere a variável DB_DATABASE=laravel para DB_DATABASE=clients

Defina a chave de criptografia do Laravel com o comando:

```
php artisan key:generate
```
Inicie o servidor com o comando:

```
php artisan serve
```

Para visualizar o banco de dados, clique no botão "Admin" no MySQL a partir do XAMPP.



## Populando o banco de dados

Para popular o banco de dados com clientes fictícios, foi utilizado o arquivo `seed.php`. Para executá-lo, digite o comando:

```
php artisan db:seed
```

## Utilizando a API

Para realizar requests de inserção de clientes, foi utilizado o Insomnia. Para a documentação e validação da API, foi utilizado o Swagger. Para acessar o Swagger, basta acessar a URL: http://localhost/rest-api-laravel/documentation. A partir do Swagger, é possível testar as rotas criadas.

## Considerações finais

Esta REST API em PHP utilizando Laravel e MySQL é um exemplo simples de como criar uma API para inserção de clientes no banco de dados, realizando validações de dados. É importante ressaltar que é possível expandir essa API para incluir outras funcionalidades e regras de negócio específicas.
