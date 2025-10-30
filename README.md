# GiveHope
### Gestão de Doações Paroquiais

![Status](https://img.shields.io/badge/status-em_desenvolvimento-green)
![Laravel](https://img.shields.io/badge/Laravel-9.15-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)

GiveHope é uma plataforma digital desenvolvida para auxiliar paróquias na gestão de doações e no controle de estoque de recursos recebidos. O sistema permite que as paróquias registrem as doações, gerenciem os itens em estoque e garantam que a distribuição para a comunidade seja feita de maneira organizada e transparente.

## Objetivo

O principal objetivo do GiveHope é proporcionar uma ferramenta eficaz para a gestão das doações feitas para as paróquias, melhorando a organização interna. Além disso, o sistema busca aumentar a transparência do processo, oferecendo relatórios claros e garantindo que os recursos sejam distribuídos de forma justa e eficiente para quem mais precisa.

## Funcionalidades Principais

* **Cadastro de Doações:** Registro de todas as doações recebidas, incluindo tipo de item, quantidade e dados opcionais do doador.
* **Controle de Estoque:** Acompanhamento em tempo real dos itens no inventário, com registro de entradas e saídas.
* **Gestão de Distribuição:** Ferramentas para gerenciar a distribuição de recursos para famílias, indivíduos e outras entidades, garantindo controle e transparência.
* **Relatórios e Listagens:** Geração de relatórios detalhados sobre o volume de doações, itens em estoque e distribuições realizadas.
* **Autenticação Segura:** Sistema de login para garantir que apenas os responsáveis da paróquia acessem e gerenciem os dados.
* **Portal da Comunidade:** Área do site onde a comunidade pode acompanhar eventos de arrecadação, visualizando detalhes e quantidades, promovendo a transparência e a confiança.

## Tecnologias Utilizadas

* **Backend:** Laravel 9
* **Frontend:** Blade, Bootstrap
* **Banco de Dados:** MySQL
* **Linguagem:** PHP 8.1
* **Servidor:** Apache/Nginx
* **Controle de Versão:** Git & GitHub

## Como Executar o Projeto

Siga os passos abaixo para configurar o ambiente de desenvolvimento local.

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/ArthurSGO/givehope.git
    cd givehope
    ```

2.  **Instale as dependências do PHP:**
    ```bash
    composer install
    ```

3.  **Configure o arquivo de ambiente:**
    ```bash
    cp .env.example .env
    ```

4.  **Gere a chave da aplicação:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure o banco de dados:**
    Abra o arquivo `.env` e ajuste as variáveis de banco de dados (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

6.  **Execute as migrações:**
    ```bash
    php artisan migrate
    ```

7.  **Instale as dependências do frontend:**
    ```bash
    npm install
    ```

8.  **Compile os assets:**
    ```bash
    npm run dev
    ```

9.  **Inicie o servidor local:**
    ```bash
    php artisan serve
    ```

A aplicação estará disponível em `http://127.0.0.1:8000`.

## Criação de Usuário Administrador

Após executar as migrações, utilize o Tinker para registrar um usuário administrador manualmente:

1. Acesse o Tinker:
   ```bash
   php artisan tinker
   ```

2. No console interativo, execute o comando abaixo, ajustando `name`, `email` e `password` conforme necessário:
   ```php
   \App\Models\User::create([
       'name' => 'Administrador',
       'email' => 'admin@givehope.com',
       'password' => bcrypt('admin123'),
       'is_admin' => true,
   ]);
   ```

3. Digite `exit` para sair do Tinker.

O usuário criado poderá acessar o painel administrativo com as credenciais informadas.

## Equipe

* Arthur Soares Gardim - RA: 14526
* Guilherme Ananias Calixto Ribeiro - RA: 14652
* Kaiki Teles de Andrade - RA: 14518