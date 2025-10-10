# GiveHope
### [cite_start]Gestão de Doações Paroquiais [cite: 4]

![Status](https://img.shields.io/badge/status-em_desenvolvimento-green)
![Laravel](https://img.shields.io/badge/Laravel-9.15-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)

[cite_start]GiveHope é uma plataforma digital criada para auxiliar paróquias na gestão eficiente de doações e no controle de estoque dos recursos recebidos[cite: 6]. [cite_start]O sistema permite que as paróquias registrem as doações, gerenciem os itens em estoque e garantam que a distribuição para a comunidade seja feita de maneira organizada e transparente[cite: 7].

## 🎯 Objetivo

[cite_start]O principal objetivo do GiveHope é fornecer uma ferramenta eficaz para a gestão de doações, melhorando a organização interna das paróquias[cite: 10, 11]. [cite_start]Além disso, o sistema busca aumentar a transparência do processo, garantindo que os recursos sejam distribuídos de forma justa e eficiente para quem mais precisa[cite: 12].

## ✨ Funcionalidades Principais

* [cite_start]**Cadastro de Doações:** Registro de todas as doações recebidas, incluindo tipo de item, quantidade e dados opcionais do doador[cite: 14].
* [cite_start]**Controle de Estoque:** Acompanhamento em tempo real dos itens no inventário, com registro de entradas e saídas[cite: 15].
* [cite_start]**Gestão de Distribuição:** Ferramentas para gerenciar a distribuição de recursos para famílias, indivíduos e outras entidades, garantindo controle e transparência[cite: 16].
* [cite_start]**Relatórios e Listagens:** Geração de relatórios detalhados sobre o volume de doações, itens em estoque e distribuições realizadas[cite: 17].
* [cite_start]**Autenticação Segura:** Sistema de login para garantir que apenas os responsáveis da paróquia acessem e gerenciem os dados[cite: 18].
* [cite_start]**Portal da Comunidade:** Uma área no site onde a comunidade pode acompanhar eventos de arrecadação, visualizando detalhes e quantidades, promovendo a transparência e a confiança[cite: 19].

## 🚀 Tecnologias Utilizadas

* **Backend:** Laravel 9
* **Frontend:** Blade, Bootstrap
* **Banco de Dados:** MySQL
* **Linguagem:** PHP 8.1
* **Servidor:** Apache/Nginx
* **Controle de Versão:** Git & GitHub

## 🔧 Como Executar o Projeto

Siga os passos abaixo para configurar o ambiente de desenvolvimento local.

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/ArthurSGO/givehope.git](https://github.com/ArthurSGO/givehope.git)
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
    Abra o arquivo `.env` e configure as variáveis de banco de dados (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

6.  **Execute as migrações e seeders:**
    ```bash
    php artisan migrate --seed
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

Acesse a aplicação em `http://127.0.0.1:8000`.

## 🧑‍💻 Equipe

* [cite_start]Arthur Soares Gardim - RA: 14526 [cite: 1]
* [cite_start]Guilherme Ananias Calixto Ribeiro - RA: 14652 [cite: 2]
* [cite_start]Kaiki Teles de Andrade - RA: 14518 [cite: 3]
