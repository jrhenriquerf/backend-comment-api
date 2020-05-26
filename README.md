# Backend Comment Api

Este projeto foi desenvolvido como um desafio para a empresa Esapiens e tem como objetivo disponibilizar uma API para comentários em um post, com algumas regras de negócio envolvidas para o tal.

## Dependências

- [Docker](https://www.docker.com/get-started)
- [Docker compose](https://docs.docker.com/compose/install/)

## Tecnologias utilizadas

- [Phalcon 4.0](https://docs.phalcon.io/4.0/en/introduction)
- [Phalcon DevTools](https://docs.phalcon.io/4.0/en/devtools)
- [Docker](https://www.docker.com/why-docker)
- [Mysql](https://www.mysql.com/why-mysql/)

## Como rodar o projeto

- Clone o projeto
```
    git clone https://github.com/jrhenriquerf/backend-comment-api.git
```
- Após isso é necessário criar o arquivo `.env` a partir do `.env.example`
```
    cp .env.example .env
```
- Na pasta do projeto, rode o seguinte comando
```
    docker-compose up -d
```
> **Obs.:** Este comando irá subir containers com o servidor php (**localhost**) na **porta 8080** e o banco de dados (**localhost**) na **porta 3306**, além de rodar as migrations do banco com alguns dados.
- E pronto, você pode fazer as requições seguindo esta [documentação das APIs desenvolvidas](https://documenter.getpostman.com/)

## Regras de negócio
- Só é permitido 4 comentários por minuto por usuário
- As notificações dos usuários expirarão 1 hora após sua visualização.