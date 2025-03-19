# Controle Financeiro

Um aplicativo de controle financeiro desenvolvido com PHP, MySQL, HTML, CSS e JavaScript, seguindo a arquitetura MVC.

## Funcionalidades

- Cadastro e visualização de receitas
- Cadastro e visualização de despesas
- Dashboard com resumo financeiro
- Gráfico de distribuição de gastos
- Autenticação de usuários
- API RESTful

## Estrutura do Projeto

```
finance-app/
├── api/
│   ├── config/
│   │   └── database.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   └── TransacaoController.php
│   ├── models/
│   │   ├── Usuario.php
│   │   ├── Transacao.php
│   │   └── Categoria.php
│   └── routes/
│       ├── auth.php
│       └── transacoes.php
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   ├── index.php
│   ├── login.php
│   ├── dashboard.php
│   ├── receitas.php
│   └── despesas.php
└── database.sql
```

## Instalação

1. Clone este repositório
2. Configure um servidor web com PHP e MySQL
3. Importe o arquivo `database.sql` para criar o banco de dados
4. Configure as credenciais do banco de dados no arquivo `api/config/database.php`
5. Acesse o aplicativo pelo navegador

## Usuário Padrão

- Email: admin@email.com
- Senha: 123456

## Tecnologias Utilizadas

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript (ES6+)
- Chart.js para gráficos

## API Endpoints

### Autenticação
- POST /api/auth.php
  - action: login
  - action: logout
  - action: check

### Transações
- GET /api/transacoes.php
- POST /api/transacoes.php
  - action: create
  - action: totals
  - action: categorias

## Contribuição

Este projeto foi desenvolvido como base para que estudantes possam praticar suas habilidades em desenvolvimento web. Sinta-se à vontade para fazer pull requests e contribuir com o projeto.

## Próximos Passos

- Implementar página de relatórios
- Adicionar funcionalidade de gerenciamento de categorias
- Implementar gráficos dinâmicos com dados reais
- Adicionar funcionalidade de exportação de dados
- Implementar testes automatizados
- Adicionar documentação da API