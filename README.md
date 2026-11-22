# Emilyn Machado Saloon

Projeto final do PIC I. O site apresenta os serviços e os trabalhos do salão e permite consultar a disponibilidade, criar uma conta e reservar horários. A equipe usa a agenda para acompanhar e gerenciar os atendimentos.

## Integrantes

- Matheus Raspante
- Alexandre Rios
- Lucas
- Yuri

## Funcionalidades

- Catálogo por categoria, páginas de serviço, galeria filtrável e informações de contato.
- Calendário de disponibilidade com atendimentos de uma hora, das 9h às 18h.
- Cadastro e login com e-mail e senha. As senhas são armazenadas como hash.
- Clientes podem consultar seus agendamentos e editar ou cancelar seus horários em aberto. A equipe consulta a agenda geral, edita registros e altera a situação dos atendimentos.
- A agenda verifica conflitos de horário antes de confirmar a reserva.

## Tecnologias e requisitos

PHP 8.3 com MySQLi e mysqlnd, MariaDB 10.11, Nginx, HTML, CSS e JavaScript. Para executar com os containers do projeto, use Docker com Compose v2.24.4 ou posterior, acesso à internet para baixar as imagens, e as portas locais 8000 e 3306 livres. O BrowserSync é opcional e usa a porta 3000.

## Instalação local

Clone o [repositório do projeto](https://github.com/Raspanti/Emilyn-Machado-Saloon) ou extraia o ZIP e abra um terminal na pasta que contém `docker-compose.yml`.

Crie `.env.agenda` a partir de `.env.agenda.example`. Os valores de exemplo coincidem com os do banco de demonstração do Compose. Se já tiver um banco local, ajuste a configuração para os dados desse ambiente.

```powershell
Copy-Item .env.agenda.example .env.agenda
docker compose -f docker-compose.yml -f compose.agendamento.yaml up -d --build db app web
docker compose -f docker-compose.yml -f compose.agendamento.yaml logs --tail 30 db
```

Em Bash, use `cp .env.agenda.example .env.agenda` para copiar o modelo.

### Configurar o banco

Quando o MariaDB informar que está pronto, importe `database/agenda.sql` e `database/usuarios.sql` em um banco novo. Os scripts incluem somente a estrutura das tabelas, sem contas nem reservas.

```powershell
Get-Content -Raw database/agenda.sql | docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mariadb -u "$MYSQL_USER" "$MYSQL_DATABASE"'
Get-Content -Raw database/usuarios.sql | docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mariadb -u "$MYSQL_USER" "$MYSQL_DATABASE"'
```

Em Bash, substitua o `Get-Content ... |` pelo redirecionamento `< database/agenda.sql` ou `< database/usuarios.sql` no fim do comando correspondente.

Para um banco existente, importe `usuarios.sql` e consulte `SHOW COLUMNS FROM agenda;`. Se a tabela `agenda` existir sem `usuario_id`, rode [003-agenda-usuarios.sql](database/003-agenda-usuarios.sql). Se a coluna já existir, confira se `cliente` comporta 100 caracteres. Caso ainda seja `VARCHAR(50)`, execute `ALTER TABLE agenda MODIFY COLUMN cliente VARCHAR(100) NOT NULL;`. Agendamentos antigos sem vínculo ficam disponíveis para a equipe.

Abra o [site local](http://127.0.0.1:8000/). Para parar o ambiente e preservar os dados, use `docker compose stop`.

## Exemplos para avaliação

1. Abra o [cadastro](http://127.0.0.1:8000/cadastro.php), crie uma conta e entre pela [tela de login](http://127.0.0.1:8000/login.php).
2. Na [página de manicure](http://127.0.0.1:8000/servico.php?id=manicure), selecione uma data e um horário livre. Confirme e confira o resultado em [Meus agendamentos](http://127.0.0.1:8000/agendamento.php).
3. Entre com outro cliente e confira que o horário aparece como ocupado no calendário, enquanto os dados da reserva ficam disponíveis apenas ao titular e à equipe.
4. Para testar o painel da equipe, crie uma conta de teste pelo cadastro e promova somente essa conta no banco. Substitua o e-mail no exemplo e entre novamente.

   ```sql
   UPDATE usuarios SET perfil = 'equipe' WHERE email = 'equipe@exemplo.com';
   ```

5. Como equipe, confira a lista geral, os filtros e a alteração de situação. Para avaliar as páginas públicas, veja também o [catálogo](http://127.0.0.1:8000/servicos.php) e a [galeria](http://127.0.0.1:8000/galeria.php).

## Publicação

Configure o servidor para servir a pasta `public/`. Prepare as tabelas no banco da hospedagem antes de publicar. Se a hospedagem já tiver um `bdconnecta.php` na raiz pública que fornece a variável `$conn`, mantenha a cópia do domínio. O carregador em `public/src/bdconnecta.php` usa `DB_HOST`, `DB_USER`, `DB_PASSWORD` e `DB_NAME` quando o ambiente está configurado. Sem `DB_HOST`, ele pode usar o arquivo de conexão da raiz pública.

## Entrega do PIC I

Até **23 de novembro de 2026**, entregar no Teams o ZIP com o projeto, o link do GitHub acessível ao professor e o vídeo de apresentação ou seu link. O vídeo deve mostrar o projeto funcionando, explicar as tecnologias e os resultados e incluir todos os integrantes com a câmera aberta durante a própria apresentação. Se houver impedimento, a apresentação presencial pode ser combinada com o professor.
