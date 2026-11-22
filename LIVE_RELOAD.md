# Desenvolvimento com BrowserSync

Na pasta do projeto:

```bash
cd /home/raspanty/Dev/Emilyn-Machado-Saloon
# Iniciar somente o serviço adicional (primeira vez: constrói a imagem)
docker compose -f docker-compose.yml -f compose.browsersync.yaml up -d --no-deps --build browsersync
# Parar somente o serviço adicional
docker compose -f docker-compose.yml -f compose.browsersync.yaml stop browsersync
# Logs
docker compose -f docker-compose.yml -f compose.browsersync.yaml logs --tail 50 -f browsersync
```

Abrir http://127.0.0.1:3000 no Zen e salvar os arquivos no VSCodium.
O proxy usa `web:80`, na rede padrão existente do projeto. A aplicação precisa estar em execução.
Para iniciar os containers originais já existentes, use `docker compose start app db web adminer`.
Não é necessário reconstruir a imagem ao editar PHP/CSS/JS/HTML.

CSS usa injeção quando possível; PHP, HTML e JS disparam atualização da página.
Somente `public/` é observado e montado como leitura no BrowserSync, sem polling.
Não há interface administrativa BrowserSync ou túnel público. Apenas a porta nova
3000 é publicada em loopback; as portas originais do projeto permanecem intactas.

BrowserSync 3.0.4 e dependências são fixados em package-lock.json; `npm ci` roda
durante o build, nunca a cada início. Não se instala Node no host. A imagem base
`node:22-alpine` acompanha atualizações da série 22: builds futuros podem variar
na base, embora o lockfile fixe as dependências npm.

O audit do lockfile em 2026-11-17 reportou ReDoS em picomatch e seus dependentes
(6 entradas altas, uma cadeia). Não foi aplicado downgrade automático. Os padrões
observados são fixos na configuração e o serviço de desenvolvimento fica em loopback.

## Reversão

Pare apenas o BrowserSync com o comando acima. Se quiser remover o container:

```bash
docker compose -f docker-compose.yml -f compose.browsersync.yaml rm -f browsersync
```

Depois arquive `compose.browsersync.yaml`, `docker/browsersync/` e este documento
fora do projeto. Não use `down -v`. Nenhum arquivo original de Docker foi editado.
A correção autorizada dos dois includes em `public/index.php` tem backup em
`.dev-backups/20261117-154507/index.php`; restaure somente esse arquivo para desfazê-la.
O backup fica fora da raiz pública e não deve ser commitado.

## Limitação preexistente da aplicação

Faltam `public/src/includes/user-menu-items.php` (require fatal no cabeçalho)
e `public/js/script.js` (referenciado no HTML). O conteúdo desses arquivos não
foi inventado. HTTP 200 sozinho não comprova que o PHP renderizou a página toda.

## Validação em 2026-11-17

- Compose válido; PHP index.php sem erros de sintaxe.
- Containers existentes iniciados por compose start, sem recriação. MariaDB pronto para conexões no volume original.
- Proxy e CSS HTTP 200; cliente BrowserSync injetado em resposta Accept: text/html e script HTTP 200.
- Socket.IO conectado: alteração CSS emitiu file:reload com type=inject; PHP, HTML e JS emitiram browser:reload. Arquivos temporários removidos.
- Zen iniciado na URL local; log BrowserSync confirmou navegador Firefox 159 conectado (Zen).
- Bind Docker confirmado em 127.0.0.1:3000. Compose original, portas e volume MariaDB não editados.
- Inspeção visual da injeção CSS não realizada; aplicação ainda gera fatal por include ausente.


## 2026-11-17 — mescla básica do MEAW

A estrutura mobile completa foi retirada para a pasta irmã de referência.
O projeto atual contém somente header, footer, CSS e JS do MEAW, com main vazio.
Os includes user-menu-items.php e scripts.js agora existem; o erro de arquivo
ausente descrito na primeira validação foi resolvido pela mescla.
Backup imediatamente anterior: `.dev-backups/pre-mescla-meaw-20261117-163133`.
Validados PHP/JS, HTTP 200, HTML único, IDs únicos, popup, tema e menu mobile.


## 2026-11-21 — retomada da versão completa

Versão completa restaurada na pasta principal com nomes inspirados no MEAW.
Ver GUIA_DESENVOLVIMENTO.md. O Docker estava inativo nesta verificação; a URL
127.0.0.1:3000 depende de iniciar o Docker e os serviços do projeto.


## Agendamento integrado

O .env agora seleciona também compose.agendamento.yaml. Para iniciar os containers
existentes, use docker compose start na pasta do projeto. Não recrie app apenas
com o Compose base: a imagem de desenvolvimento com mysqli vem do override.
Veja GUIA_DESENVOLVIMENTO.md para detalhes, testes e backup do painel.
