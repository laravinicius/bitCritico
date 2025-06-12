# Bit Crítico

**Bit Crítico** é uma plataforma web para avaliação e review de jogos. O projeto foi desenvolvido como parte de um trabalho de prática profissional, com o objetivo de criar um espaço onde usuários podem se cadastrar, pesquisar por jogos, ler análises de outros usuários e compartilhar suas próprias opiniões.

A aplicação conta com um sistema de autenticação, perfis de usuário, um catálogo de jogos pesquisável e um painel administrativo para gerenciamento de conteúdo.

**Link do projeto em produção:** [bitcritico.azurewebsites.net](https://bitcritico-ekgaccgyfygkd2e3.canadacentral-01.azurewebsites.net)

## ✨ Funcionalidades

### Para Usuários
* **Cadastro e Login:** Sistema de autenticação seguro para usuários.
* **Catálogo de Jogos:** Navegue por uma lista de jogos, com filtros por gênero e desenvolvedora.
* **Detalhes do Jogo:** Veja informações detalhadas de cada jogo, incluindo sinopse, capa, e a média de notas das avaliações.
* **Sistema de Reviews:**
    * Usuários logados podem criar, editar e excluir suas próprias reviews para os jogos.
    * É possível atribuir uma nota de 0 a 10 e uma descrição.
    * Sistema de "likes" para as reviews de outros usuários.
* **Perfil de Usuário:**
    * Uma página de perfil exibe as informações do usuário e suas reviews mais recentes e mais curtidas.
    * Gerenciamento completo das próprias reviews (editar e excluir).
* **Busca e Filtros:** Pesquise jogos pelo nome e filtre a lista por gênero ou desenvolvedora.
* **Chatbot Assistente:** Um chatbot ("BitCri") está disponível para ajudar os usuários a encontrar informações e recomendações de jogos.

### Para Administradores
* **Painel Administrativo:** Uma área restrita para gerenciamento do conteúdo da plataforma.
* **Gerenciamento de Conteúdo:**
    * Adicionar novos jogos, gêneros, plataformas e desenvolvedoras ao banco de dados.
* **Gerenciamento de Usuários:** Administradores podem visualizar todos os usuários cadastrados e alterar seus status (promover para administrador ou revogar acesso).

## 🚀 Tecnologias Utilizadas

* **Backend:** PHP 8.4
* **Frontend:** HTML5, CSS3, JavaScript
* **Banco de Dados:** MySQL
* **Hospedagem:**
    * **Aplicação:** Azure Web App
    * **Banco de Dados:** Railway
* **Automação (CI/CD):** GitHub Actions para deploy contínuo na Azure.

## 🗄️ Estrutura do Banco de Dados

O banco de dados é relacional e consiste nas seguintes tabelas para gerenciar usuários, jogos, reviews e suas inter-relações:

* `Usuario`: Armazena os dados dos usuários, como nome, e-mail e senha (hash).
* `Jogo`: Contém as informações dos jogos, como título, ano de lançamento, descrição e capa.
* `Review`: Guarda as avaliações feitas pelos usuários para cada jogo.
* `Genero`, `Plataforma`, `Desenvolvedora`: Tabelas para categorizar os jogos.
* `Like_Review`: Registra os "likes" que cada review recebeu.
* `Jogo_Genero` e `Jogo_Plataforma`: Tabelas de associação para os relacionamentos N:N.

A estrutura completa pode ser visualizada no arquivo [SQL banco.txt](SQL%20banco.txt).

## 🔧 Guia de Instalação Local

Para rodar este projeto em um ambiente de desenvolvimento local, siga os passos abaixo.

### Pré-requisitos

* Um servidor web local com suporte a PHP e MySQL (ex: XAMPP, WAMP, Laragon).
* Um gerenciador de banco de dados (ex: phpMyAdmin, DBeaver).
* Git (opcional, para clonar o repositório).
