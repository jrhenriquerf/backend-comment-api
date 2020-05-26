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
```bash
    git clone https://github.com/jrhenriquerf/backend-comment-api.git
```
- Após isso é necessário criar o arquivo `.env` a partir do `.env.example`
```bash
    cp .env.example .env
```
- Na pasta do projeto, rode o seguinte comando
```bash
    docker-compose up -d
```
> **Obs.:** Este comando irá subir containers com o servidor php (**localhost**) na **porta 8080** e o banco de dados (**localhost**) na **porta 3306**, além de rodar as migrations do banco com alguns dados.
- E pronto, você pode fazer as requições seguindo esta [documentação das APIs desenvolvidas](https://documenter.getpostman.com/)

## Detalhes da solicitação e regras de negócio aplicadas
### Postagem de um comentário
- Um usuário não pode efetuar mais de **5** comentários em **60** segundos.

- Um usuário não assinante não poderá comentar em uma postagem de outro usuário
não assinante.
    - Caso ambos os usuários não sejam assinantes, mas quem está realizando o comentário está comprando destaque, o comentário deverá ser liberado.

### Compra de destaque
- A compra de destaque possibilita que um comentário seja exibido com prioridade na
listagem. Esta prioridade é calculada como “ganho de tempo”
    - O sistema **retém 10%** do valor pago pela compra e cria uma transação de entrada para o valor retido e uma de saída para o usuário.
    - Um comentário feito com 100 moedas garante ao usuário que comentou uma prioridade de 100 minutos na listagem de comentários de uma postagem.
     >  **Exemplo:** um comentário feito às 20:00 na postagem X com 100 moedas de destaque será exibido em primeiro lugar até as 21:40 (100 minutos). Caso mais de um comentário com destaque seja postado neste período o critério de desempate será o número de moedas enviado.

### Regras para listagem
- Os comentários deverão ser listados em ordem cronológica (do mais novo para o mais antigo).
- Comentários com compra de destaque devem ser identificados mesmo que o período de destaque tenha se encerrado.
- A API deve implementar paginação;
-  Cada comentário listado deverá trazer as seguintes propriedades:
```json
{
    "id do usuário": "user.id",
    "id do comentário": "id",
    "login": "user.username",
    "assinante": "user.subscriber",
    "destaque": "highlight",
    "data/hora": "date",
    "comentário": "comment"
}
```
> Comparação entre os dados solicitados e os atributos da API liberada.

### Regras para exclusão
- Somente o dono de um comentário ou o dono da postagem no qual o
comentário foi feito tem permissão para excluir um comentário.
    - Se o usuário for dono da postagem ele pode excluir todos os
comentários de um usuário que comentou na postagem relacionada.

### Notificações
- A notificação do sistema é expirada (não é mais listada) **1 hora** após sua exibição (consulta da API)
- Enviar uma notificação (sistema e e-mail) para o dono da postagem avisando que o usuário X comentou em sua postagem Y.

## Estrutura do banco de dados
- A desenvolver