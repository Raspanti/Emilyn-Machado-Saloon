# Emilyn Machado Saloon

Projeto acadêmico em equipe para desenvolver um site de salão de beleza.

> **Estado atual:** repositório em fase de estruturação. As funcionalidades de agendamento e integrações ainda não estão implementadas/validadas neste código. A página inicial contém componentes provisórios de outro projeto.

## Objetivo do projeto

Planejamos construir uma aplicação web que permita apresentar o salão e seus serviços, organizar agendamentos e facilitar o contato com clientes. A equipe ainda precisa confirmar os requisitos e o escopo de cada integração, inclusive Instagram e WhatsApp.

## Tecnologias encontradas no repositório

- **PHP 8.3 (FPM)** — aplicação no servidor.
- **Nginx** — servidor web.
- **MariaDB 10.11** — banco de dados.
- **Adminer** — interface de administração do banco.
- **Docker Compose** — ambiente de desenvolvimento.

Essas versões/serviços constam no `docker-compose.yml`; isso **não significa que o sistema já esteja funcionando integralmente**.

## Estrutura atual

```text
.
├── docker/
│   └── nginx/default.conf    # Configuração Nginx
├── public/
│   ├── css/style.css         # Estilos
│   ├── index.php             # Página inicial provisória
│   └── src/includes/         # Cabeçalho e rodapé
├── .gitignore
├── docker-compose.yml
├── CONTRIBUTING.md           # Como trabalhar em equipe com Git
└── README.md
```

## Começando a colaborar

1. Crie uma conta no GitHub e peça acesso de colaborador ao responsável pelo repositório.
2. Instale Git e clone o projeto:
   ```bash
   git clone https://github.com/Raspanti/Emilyn-Machado-Saloon.git
   cd Emilyn-Machado-Saloon
   ```
3. Abra uma [Issue](https://github.com/Raspanti/Emilyn-Machado-Saloon/issues) para combinar a tarefa antes de alterar arquivos.
4. Leia o **[guia de contribuição](CONTRIBUTING.md)**. Cada tarefa deve ser desenvolvida em uma branch própria e enviada por Pull Request.

**Não faça commits diretamente em `main`.** A branch principal deve receber alterações revisadas.

## Executar localmente — pendente de validação

O repositório contém um `docker-compose.yml`, mas antes de iniciar os serviços precisamos corrigir o esqueleto e revisar a configuração de desenvolvimento.

Problemas identificados no estado atual:

- `public/index.php` procura `../src/includes/header.php` e `footer.php`, mas os arquivos estão em `public/src/includes/`.
- O cabeçalho referencia `user-menu-items.php` e um script JavaScript que não aparecem no código publicado.
- Há conteúdo herdado de um projeto de adoção de animais, não do salão.
- O Compose contém senhas de exemplo fixas e publica portas do banco e do Adminer. **Não utilizar esse ambiente em produção nem inserir dados reais de clientes.**

A equipe deve corrigir e validar esses pontos em Issues/PRs separados antes de considerar a aplicação pronta.

## Segurança e dados

Não publique senhas, tokens, dados pessoais de clientes, arquivos `.env`, exports de banco ou backups. O `.gitignore` já ignora vários arquivos locais, mas isso não substitui a revisão antes de `git add` ou `git push`.

## Trabalho em equipe

- Uma tarefa por Issue; indique quem está responsável.
- Uma branch por tarefa: `feat/agendamento`, `fix/menu-mobile` ou `docs/instrucoes`.
- Pull Requests pequenos, com explicação e, quando possível, capturas da interface sem dados privados.
- Pelo menos outro integrante revisa antes do merge.
- Dúvidas e decisões técnicas devem ficar registradas na Issue ou no PR.
