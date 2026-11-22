# Emilyn Machado Saloon

Site do salão com catálogo de serviços, galeria e agendamento. O ambiente local usa PHP, Nginx, MariaDB e BrowserSync com Docker Compose. A agenda oferece blocos de 1 hora, das 9h às 18h.

## Integrantes

- Matheus Raspante
- Alexandre Rios
- Lucas
- Yuri

## Funcionalidades

- Catálogo por categoria, com acesso direto quando há um único serviço.
- Galeria filtrável por serviço e navegação adaptada a celulares.
- Consulta de disponibilidade e agendamento por serviço, em blocos de uma hora.
- Login com Google, consulta dos próprios horários e painel administrativo para gerenciar atendimentos.

## Tecnologias e requisitos

PHP 8.3, Nginx, MariaDB 10.11, HTML, CSS, JavaScript e BrowserSync. Use Docker em execução com Docker Compose v2 recente (2.24.4 ou superior para a diretiva `!override`), acesso à internet para baixar as imagens e portas locais 3000 e 8000 livres. O Node.js do BrowserSync roda no container. O login requer um cliente OAuth Web próprio do avaliador ou do grupo.

## Subir o ambiente local

Instale Docker com o plugin Compose v2 e execute os comandos na raiz do projeto. O Docker deve estar em execução e acessível à sua conta.

Copie o modelo de configuração do banco. Os valores do modelo coincidem com os padrões de demonstração do Compose. Se você usar um volume existente, informe as credenciais com que ele foi inicializado.

```sh
cp -n .env.agenda.example .env.agenda
```

```sh
docker compose -f docker-compose.yml -f compose.browsersync.yaml -f compose.agendamento.yaml up -d --build db app web browsersync
docker compose -f docker-compose.yml -f compose.browsersync.yaml -f compose.agendamento.yaml ps
```

- [Site com atualização automática](http://127.0.0.1:3000/). Use este endereço para testar o login Google com a configuração padrão.
- [Site direto no Nginx](http://127.0.0.1:8000/). Útil para conferir o catálogo sem BrowserSync.

## Preparar o banco

Aguarde o MariaDB informar que está pronto para conexões em `docker compose logs --tail 30 db`. Confira quais tabelas já existem:

```sh
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mariadb -u "$MYSQL_USER" "$MYSQL_DATABASE" -e "SHOW TABLES"'
```

Em um banco novo, importe os dois arquivos na ordem abaixo. Em um banco existente, execute apenas o comando correspondente à tabela ausente. `agenda_contas` depende de `agenda`. Se ambas existirem, pule a importação. Os arquivos SQL criam a estrutura e não inserem registros de clientes.

```sh
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mariadb -u "$MYSQL_USER" "$MYSQL_DATABASE"' < database/agenda.sql
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mariadb -u "$MYSQL_USER" "$MYSQL_DATABASE"' < database/002-agenda-contas.sql
```

Rode novamente `SHOW TABLES` para confirmar a presença de `agenda` e `agenda_contas`. O volume do MariaDB preserva os dados entre execuções. Use `docker compose stop` para parar os serviços e preserve o volume ao atualizar o projeto.

## Habilitar o login e conferir a agenda

1. Crie um cliente OAuth Web na Google Auth Platform. Copie `config/google.example.php` para `config/google.local.php`. Preencha o ID e o segredo do cliente OAuth Web localmente. O PHP no container precisa conseguir ler o arquivo, que fica fora da pasta pública e é ignorado pelo Git.
2. No cliente OAuth, autorize o retorno exato `http://127.0.0.1:3000/google-retorno.php`. Se o aplicativo estiver em modo de teste, inclua a conta que será usada entre os usuários de teste. Preencha `admin_emails` apenas para contas que devam administrar a agenda.
3. Abra a [página de manicure](http://127.0.0.1:3000/servico.php?id=manicure), escolha um dia e um horário, entre com uma conta de cliente e confirme o cadastro. Confira o registro em **Meus agendamentos** e consulte o mesmo horário em outra sessão para verificar o bloqueio.

O endereço padrão aceita conexões apenas na própria máquina. Para testar a interface em dimensões de celular, use o modo responsivo do navegador no endereço acima.

## Exemplos

- [Catálogo de serviços](http://127.0.0.1:3000/servicos.php)
- [Galeria](http://127.0.0.1:3000/galeria.php)
- [Calendário de Pedicure](http://127.0.0.1:3000/servico.php?id=pedicure)

O catálogo e a galeria podem ser avaliados antes de configurar o Google. Para gravar e gerenciar agendamentos, conclua a configuração do banco e do OAuth acima. As imagens com a legenda “Imagem ilustrativa” são referências visuais geradas para o projeto.

Mais detalhes em [GUIA_DESENVOLVIMENTO.md](GUIA_DESENVOLVIMENTO.md) e [LIVE_RELOAD.md](LIVE_RELOAD.md).
