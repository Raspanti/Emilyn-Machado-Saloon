> Para instalar e testar a versão atual, consulte o [README](README.md). O fluxo OAuth descrito no histórico foi substituído pelo cadastro e login com e-mail e senha.

# Entendendo a versão para celular

Abra http://127.0.0.1:3000/ com Docker e BrowserSync em execução.

## O que foi reaproveitado do MEAW

A separação em includes PHP, o reset CSS, as variáveis e a ideia de separar
comportamento JavaScript da marcação HTML. A identidade de adoção, o popup de
login e o modo escuro não foram transportados: os esboços do salão têm outra
navegação e ainda não existe autenticação implementada.

O projeto MEAW original foi apenas consultado e não recebeu alterações.

## Como uma página é montada

`index.php` define o título, inclui `src/includes/header.php`, escreve o conteúdo
dentro de `main` e inclui `src/includes/footer.php`.

O header é o único que abre `html`, `head` e `body`. O footer é o único que
carrega `/js/scripts.js` e fecha o documento. Isso evita os documentos HTML
aninhados e os eventos de clique duplicados que existiam no MEAW.

O antigo menu de adoção e os requires do arquivo ausente foram substituídos
pela navegação do salão. O JS inexistente `script.js` foi substituído pelo novo
`scripts.js`, carregado uma única vez. Não há login fictício.

## Onde editar

| Arquivo | Responsabilidade |
| --- | --- |
| `public/index.php` | Página inicial |
| `public/servicos.php` | Categorias e lista de serviços da categoria |
| `public/servico.php` | Modelo único para todos os detalhes de serviço |
| `public/sobre.php` | Apresentação do salão |
| `public/contato.php` | Contato e serviço escolhido |
| `public/src/catalog.php` | Categorias, nomes e textos dos serviços |
| `public/src/site.php` | Nome, WhatsApp, Instagram e endereço confirmados |
| `public/src/includes/header.php` | Marca e navegação superior |
| `public/src/includes/footer.php` | Rodapé, barra inferior e carregamento do JS |
| `public/src/includes/category-grid.php` | Grade de categorias reutilizada |
| `public/src/bootstrap.php` | escapeHtml e renderIcon |
| `public/css/style.css` | Cores, tipografia e responsividade |
| `public/js/scripts.js` | toggleMenuMobile, closeMenuMobile e backToTop |

Os caminhos acima são relativos à pasta do projeto. O diretório `public/src`
contém apenas código de apresentação e catálogo público: não coloque senhas nele.

## Por que mobile first

O CSS fora de `@media` atende ao celular: duas colunas, cabeçalho compacto e
barra fixa inferior. `min-width: 600px` ajusta o tablet; `min-width: 900px`
ativa a navegação de desktop e retira a barra inferior.

`minmax(0, 1fr)` permite que as colunas encolham sem estourar a largura da tela.
O espaçamento inferior no body reserva espaço para a barra. A variável
`env(safe-area-inset-bottom)` protege a área de gestos em aparelhos com recorte.
O menu informa `aria-expanded`, funciona por clique e fecha por Escape ou clique
fora. As animações respeitam a preferência por movimento reduzido.

## Conteúdo provisório e agendamento

As categorias seguem os esboços. As ilustrações SVG e os textos dos serviços
são provisórios para revisão: não representam fotos ou catálogo comercial
confirmado. Não foram inventados preços, horários, depoimentos ou credenciais.

Os contatos estão vazios em `public/src/site.php`. Ao preencher WhatsApp com
país + DDD + número, somente dígitos, a página exibe o link de conversa; o serviço
escolhido vai no texto inicial. Instagram deve usar uma URL HTTPS completa.
Nenhuma mensagem é enviada automaticamente. O painel local de agendamento agora
grava no MariaDB, conforme a seção de integração abaixo; ainda não há autenticação
ou agendamento público para clientes.

A tipografia usa Google Fonts com fontes locais de fallback: se estiver offline,
o conteúdo continua legível com Georgia e sans-serif.

## Exercícios para aprender

1. Troque `--sage` no início do CSS e salve. Observe a injeção sem F5.
2. Edite o texto de Luzes em `catalog.php`. Observe o reload automático do PHP.
3. Veja como `servico.php?id=luzes` procura a chave `luzes` no catálogo.
4. Abra o modo responsivo do navegador e compare 390, 768 e 1366 pixels.

## Backup e reversão da retomada

O estado anterior (mescla básica do MEAW) está preservado em
`.dev-backups/pre-restaurar-completo-20261121-120947/public/`.
Esse backup contém também `user-menu-items.php`, que não é usado na versão completa.
Para reverter, primeiro arquive o estado atual e restaure esse public, retirando
apenas os arquivos da versão completa ausentes no backup. Não use git reset ou
limpeza generalizada, pois há alterações locais sem commit.

## Verificações da primeira versão em 2026-11-17

- PHP: 11 arquivos passaram em `php -l`; JavaScript passou em `node --check`.
- 34 URLs locais, incluindo links, CSS e JS: nenhuma falha ou erro PHP.
- Categoria/serviço inválidos respondem 404.
- Home sem overflow horizontal em 320 e 768 px; revisão visual em 390 e 1366 px.
- Menu mobile: abrir e fechar por Escape; fluxo Cabelo → Luzes → contato mantém Luzes.
- Console do navegador sem erros na consulta final.
- CSS real: propriedade temporária entrou com menu ainda aberto, sem reload total.
- PHP real: edição temporária provocou reload; ambos os arquivos foram restaurados.
- Cinco containers continuaram em execução, sem alteração no Compose ou no banco.


## Retomada em 2026-11-21

Versão completa restaurada na pasta principal, sem commit ou merge. A branch
main já estava selecionada. Backup do estado anterior em `.dev-backups/pre-restaurar-completo-20261121-120947`.

O JavaScript segue os nomes do MEAW: btnMobile, nav, toggleMenuMobile e classe
active. Funções nomeadas ficam separadas dos addEventListener para facilitar
a leitura. closeMenuMobile fecha o menu; backToTop volta ao início.

Os auxiliares PHP agora têm nomes explícitos: escapeHtml protege a saída HTML
e renderIcon gera um SVG. Variáveis CSS familiares: --bg-light, --text,
--text-muted, --border-muted e --font-family-main. Não houve mudança intencional
na aparência da versão completa. A pasta de referência e o MEAW não foram editados.


### Validação da retomada em 2026-11-21

PHP CLI local 8.5.12: 11 arquivos passaram no lint; 32 páginas/variações
renderizadas sem erros, sem documentos ou IDs duplicados. Categorias e serviços
inválidos retornaram 404. JavaScript passou em node --check.
O Docker estava inativo; a execução no PHP 8.3 do container, o BrowserSync e
as interações de navegador não foram revalidados nesta retomada.


## 2026-11-21 — painel de agendamento integrado

O painel local está em http://127.0.0.1:3000/agendamento.php. Os links Agendar
na barra inferior, no header e nos serviços agora levam a ele. O serviço escolhido
aparece nas observações; cabelo seleciona Cabeleireira e unhas seleciona Manicure.
As outras categorias ainda não têm tipo próprio: escolha um dos dois tipos do painel.

### Fluxo dos arquivos

- agendamento.php: formulário, pesquisa e lista de atendimentos.
- agendar.php: recebe o cadastro por POST.
- editarAgenda.php: busca o registro e mostra o formulário preenchido.
- atualizarAgenda.php: recebe a edição por POST.
- situacaoAgenda.php: altera Aberto, Atendido ou Faltoso.
- deleteAgenda.php: exclui via POST, com confirmação no navegador.
- src/includes/formAgenda.php: formulário compartilhado por cadastro e edição.
- src/agenda.php: funções de validação, consultas preparadas e ações comuns.
- src/bdconnecta.php: conexão mysqli com as variáveis do Docker local.
- js/agenda.js: função envio e confirmação de exclusão.
- css/agenda.css: estilos do painel carregados apenas nas páginas da agenda.

Os nomes cliente, data, hora, horaFim, tipo, obs e os nomes dos arquivos foram
mantidos do trabalho enviado. A função executarAcaoAgenda centraliza consultas
para não repetir a validação e a proteção de formulário em quatro arquivos.
O PHP valida mesmo quando o JavaScript está desligado. O token de sessão protege
os formulários contra envio por outra página; consultas usam parâmetros e a
saída usa escapeHtml. Pesquisa por tipo usa igualdade, corrigindo o != original.
A listagem mostra até 200 resultados; use os filtros para restringir a consulta.

### Banco e regra de horário

Foi criada somente a estrutura agenda no banco MariaDB local existente; não
foram importados nomes/clientes do SQL enviado. Não foi utilizada a conexão
externa bdconnecta.php do ZIP. O campo DTHR agora é DATETIME, porque o código
grava data e hora completas; a tabela usa InnoDB e utf8mb4 para acentos.
Não existe DROP ou importação automática ao iniciar o Docker.

Regra inicial: um atendimento aberto de cada tipo por intervalo. Manicure e
Cabeleireira podem ocorrer simultaneamente; dois do mesmo tipo não. Horários
adjacentes são permitidos. Não há cadastro de profissionais, duração automática,
feriados, expediente ou bloqueio de datas passadas. É possível registrar histórico.
Mudar a situação para Aberto também verifica conflitos. Um bloqueio de banco
serializa as gravações para evitar dois pedidos ocupando o mesmo intervalo.

O painel é local e não tem autenticação de funcionários. As portas do site
8000 e BrowserSync 3000 estão limitadas a 127.0.0.1. Não é uma agenda pública
para clientes. Não houve alteração nas portas anteriores do MariaDB/Adminer.

### Docker e comandos

A imagem PHP de desenvolvimento em docker/php/Dockerfile habilita mysqli.
compose.agendamento.yaml contém a imagem, o env_file e o bind local do Nginx.
O .env contém apenas COMPOSE_FILE para selecionar os três arquivos Compose.
Isso evita que docker compose up volte sem querer à imagem PHP sem mysqli.
.env.agenda contém as credenciais locais e está ignorado pelo Git. Não o publique.

Na pasta do projeto:

```bash
docker compose start
# Se alterar o Dockerfile:
docker compose build app
docker compose up -d --no-deps app web
# Ver os logs PHP:
docker compose logs --tail 40 app
```

Para um checkout sem .env, use explicitamente:

```bash
docker compose -f docker-compose.yml -f compose.browsersync.yaml -f compose.agendamento.yaml start
```

### Backup e reversão

Backup desta etapa: .dev-backups/pre-agendamento-20261121-122152/.
Inclui public original e banco-antes.sql (o banco estava sem tabelas).
Para reverter, arquive o estado atual, restaure os arquivos de public que foram
alterados e retire os arquivos novos do módulo. Retire a seleção do override em
.env e só então recrie app/web com a configuração anterior se desejar.
Recriar com a configuração anterior republica a porta 8000 como era originalmente;
revise o bind antes. Recriação provoca uma breve interrupção do site.
Preserve a tabela agenda e os dados cadastrados, mesmo se retirar o módulo.
Não importe banco-antes.sql por cima de dados novos e nunca use down -v.
O backup não contém .env nem .env.agenda, pois não existiam antes desta integração.

### Validação

PHP 8.3.32 com mysqli: sintaxe de todos os arquivos aprovada; JS validado.
Testes HTTP reais: cadastro, pesquisa por cliente/tipo/data/situação, edição,
exclusão, três situações, CSRF, métodos HTTP, escape HTML, entradas inválidas,
conflitos e horários adjacentes. Os três registros temporários foram removidos.
Fluxo Luzes → Agendar preencheu Cabeleireira e Serviço: Luzes no navegador.
Painel sem overflow horizontal em 320 e 1366 px; console sem erros.
Container do banco e instante de início preservados; PHP e Nginx recriados
somente após autorização específica.


## 2026-11-21 — navegação móvel simplificada

O menu hambúrguer foi retirado. A navegação no celular usa apenas a barra
inferior Agendar/Início/Topo; o header exibe a marca. Os links de desktop só
aparecem a partir de 900px. scripts.js agora mantém apenas backToTop.
Os trechos anteriores do guia sobre toggleMenuMobile descrevem a etapa anterior.


## Barra fixa e teclado virtual

A barra é renderizada uma única vez pelo header, por meio de bottom-nav.php.
O footer agora contém apenas o rodapé e os scripts. No CSS, fixed mantém a barra
fora do fluxo; padding e scroll-padding reservam espaço para o conteúdo.

Em scripts.js, atualizarBarraInferior usa a VisualViewport para calcular quanto
o teclado encobriu a área do layout e elevar a barra até o limite visível.
ResizeObserver mantém o espaço reservado igual à altura real da barra + 16px.
Os eventos são agrupados em requestAnimationFrame, sem polling e sem mexer no
conteúdo dos campos. O desktop continua usando só a navegação superior.

Fonte técnica: https://developer.mozilla.org/en-US/docs/Web/API/VisualViewport
Backup e evidências desta etapa estão no CHANGELOG. O teste com teclado virtual
foi simulado; confirmar o comportamento em um aparelho físico continua pendente.


## 2026-11-21 — galeria por serviço

Abra http://127.0.0.1:3000/galeria.php. A página também pode ser acessada pelo
convite na home, pelo rodapé e pelo menu de desktop. A barra inferior móvel
continua com Agendar/Início/Topo.

### Como as partes se relacionam

1. public/images/galeria guarda sete imagens únicas, copiadas sem alterar bytes
   das referências. As cópias por categoria ficaram só na pasta de referências.
2. public/src/galeria.php contém dois arrays: servicosGaleria nomeia os filtros;
   fotosGaleria reúne arquivo, título, descrição, alt, origem, dimensões e serviços.
3. public/galeria.php lê o parâmetro servico da URL e percorre o array com foreach.
   Em todas, mostra cada imagem uma vez; em um serviço, usa in_array para escolher
   as fotos. O filtro funciona mesmo sem JavaScript. Uma categoria desconhecida
   retorna 404 com links para os filtros válidos.
4. public/css/galeria.css usa as variáveis de cores e fontes existentes. A grade
   tem uma coluna no celular, duas a partir de 600px e três a partir de 1100px.
5. public/js/galeria.js acrescenta abrirFoto e fecharFoto. O dialog nativo prende
   o foco enquanto aberto; Escape fecha e devolve o foco ao cartão. Sem JS, o
   link abre o arquivo da imagem normalmente em outra aba.

O header e footer carregam CSS/JS da galeria somente quando galleryPage está
ativo. As URLs têm hash de versão, como os outros assets do site, evitando
misturar estilos antigos em cache. O CTA adicional do header fica oculto de
900 a 1100px para acomodar o novo link Galeria sem overflow.

### Como adicionar uma foto

Copie uma imagem para public/images/galeria e acrescente uma entrada em
public/src/galeria.php. Use um arquivo e um título por imagem; em servicos,
informe uma ou mais chaves existentes, por exemplo ['corte', 'luzes'].
Preencha alt descrevendo a imagem e origem com o link da publicação. Informe
largura e altura reais para reservar espaço antes do carregamento da foto.
Não é necessário copiar a imagem para cada serviço: os filtros usam as etiquetas.

As miniaturas usam object-fit: cover para manter cartões regulares; isso pode
cortar a prévia, mas o arquivo não é editado. A ampliação usa contain para mostrar
a foto inteira. Fotos posteriores às duas primeiras são carregadas sob demanda.
Os registros Antes estão identificados para não confundir com resultados finais.

### Backup e validação

Backup prévio: .dev-backups/pre-galeria-20261121-182114/.
Para reverter, primeiro arquive o estado atual, restaure header, footer, index e
style.css desse backup e mova os novos arquivos da galeria para fora de public.
Mantenha as referências originais. A galeria não usa nem modifica o banco.

Validados: PHP e JS; todos os arquivos de imagem e assets HTTP 200; filtros com
7/5/2/1/1/1 resultados (Todas/Corte/Luzes/Coloração/Hidratação/Sobrancelhas), sem
imagens duplicadas no filtro; categoria inválida 404. No navegador: filtro Luzes,
abertura da imagem inteira, Escape e retorno do foco. Sem overflow em 320, 900 e
1366px; barra inferior fixa no celular; console sem erros.


## 2026-11-21 — fotos nos cartões e detalhes dos serviços

As fotos da galeria agora substituem os desenhos onde há referência confirmada:
Cabelo e Sobrancelha nas categorias; Corte, Luzes, Coloração, Tratamento capilar
e Design nas listas e páginas de serviço. Escova, unhas, massagem, depilação e
produtos continuam com as ilustrações existentes por falta de foto específica.

### Associação centralizada

public/src/fotos-servicos.php reutiliza os dados de public/src/galeria.php.
A função buscarFotoGaleria procura o nome do arquivo e devolve os mesmos dados:
caminho, alt e dimensões. Se não encontrar o nome no catálogo, devolve null.

fotosCategorias usa a chave da categoria, como cabelo e sobrancelha.
fotosServicos usa o identificador do serviço, como corte e tratamento-capilar.
Por exemplo:

```php
'corte' => buscarFotoGaleria('2025-12-15-corte-02-depois.jpg'),
```

Para trocar a foto de Corte, altere apenas o nome nessa associação, usando um
arquivo já cadastrado na galeria. Não é preciso copiar a imagem outra vez.
Os originais de referência e a galeria não foram modificados.

### Como os modelos decidem o que mostrar

category-grid.php consulta fotosCategorias para os cartões da home e da lista
geral. servicos.php consulta fotosServicos para cada atendimento da categoria.
servico.php usa a mesma associação no destaque da página do atendimento.
O if ($foto) mostra img; o else mantém renderIcon e a decoração anterior.

A classe has-photo retira as órbitas decorativas somente quando há imagem.
object-fit: cover preenche o espaço nos cartões, sem editar os arquivos.
A classe photo-comparison usa contain para manter o antes/depois inteiro.
Tratamento capilar recebe a montagem de corte, luzes e hidratação, com uma nota
explicando que a imagem corresponde a um atendimento combinado.
width/height informam as dimensões reais; loading=lazy é usado nas listas.
O destaque com foto tem mais altura para acomodar imagens verticais.

### Validação e reversão

PHP válido; fotos das 11 rotas conferidas por HTTP, sem erros ou imagem quebrada.
Galeria e agenda continuaram respondendo. Conferidos no navegador: cartões da
home, lista de cabelo, Corte em desktop, comparação de Tratamento capilar inteira,
sem overflow em 320px e com barra móvel fixa. Console sem erros.

Backup: .dev-backups/pre-fotos-servicos-20261121-182703/.
Para reverter, arquive o estado atual e restaure os arquivos listados nesse
backup; retire public/src/fotos-servicos.php depois de restaurar bootstrap.php.
Não remova as imagens da galeria: já existiam e continuam sendo usadas por ela.
Não houve alteração de banco ou de containers.


## 2026-11-21 — fotos confirmadas de manicure e escova

Após login, foram revisados o destaque Unhas e publicações antigas do perfil.
A foto de manicure do feed confirma o serviço pela legenda e mostra esmaltação
cinza/prata. A publicação de escova cita expressamente escova, design e tintura
de sobrancelhas; sua imagem mostra o cabelo finalizado.

Fontes:
- Manicure: https://www.instagram.com/emelynmachado/p/DOOORddjS_p/
- Escova: https://www.instagram.com/emelynmachado/p/DKjtjnJOd8l/

A mudança usa a mesma estrutura já existente, sem novos modelos ou CSS:
1. Os originais ficam em referencias/instagram, por serviço, com origem e hash
   registrados no catalogo.json. Um story azul também foi preservado apenas como
   referência, pois contém enquete e sua resolução é 320x569.
2. src/galeria.php recebeu duas entradas e os filtros Manicure e Escova. A galeria
   agora tem nove imagens únicas. As fotos usadas no site estão em images/galeria.
3. src/fotos-servicos.php associa a foto cinza/prata à categoria unhas e ao serviço
   manicure; a foto de cabelo é associada somente ao serviço escova.

Os templates detectam essas associações e trocam automaticamente o ícone por img.
Pedicure não usa a foto de mãos. Massagem, depilação e produtos mantêm os desenhos.
A leitura da legenda diferencia uma foto de resultado de uma simples dica ou
anúncio que mencione escova; esses últimos não foram usados como portfólio.

Validação: PHP dos dois catálogos aprovado; HTTP 200 para fotos e rotas; home com
três fotos, lista de unhas com uma, manicure/escova com foto e pedicure sem foto.
Galeria com nove resultados e um em cada novo filtro. Conferidos carregamento,
layout móvel e desktop, ausência de overflow e barra fixa; console sem erros.
Backup: .dev-backups/pre-fotos-unhas-escova-20261121-184800/.
Para reverter, restaure os catálogos desse backup e arquive os novos JPGs se não
forem mais usados. Não é necessário alterar banco, containers ou autenticação.


## 2026-11-21 — foto de produto confirmado

Foto original da capa do reel DLpjQtvuZDJ (03/07/2025): frasco do Óleo Elixir
Ilumina TOCTUS, confirmado na legenda do salão. Aplicada à categoria Produtos,
ao serviço Cuidados em casa e à galeria, com novo filtro Produtos. Estoque atual
não foi presumido. A imagem continua inteira no arquivo; o CSS existente recorta
apenas sua exibição. Nenhuma alteração de CSS, templates, banco ou containers.

Para aprender: galeria.php define os dados da foto; fotos-servicos.php associa
a imagem às chaves produtos e cuidados-em-casa. Os modelos existentes escolhem
a foto automaticamente quando há associação, preservando o estilo.

Pesquisa parcial: não foram confirmadas fotos de pedicure, depilação ou massagem
corporal. Massagem capilar vista no perfil não foi classificada como corporal.
Origem: https://www.instagram.com/emelynmachado/reel/DLpjQtvuZDJ/

Backup: .dev-backups/pre-foto-produtos-20261121-190902/
Rollback: restaure os cinco arquivos desse backup e arquive os dois novos JPGs
se não forem mais utilizados. Não requer reiniciar serviços.

Validação: PHP válido nos dois catálogos; HTTP 200 para home, categoria, serviço,
galeria e JPG, sem avisos PHP. Galeria com 10 imagens totais e 1 em Produtos.
Imagem carregada no desktop e em 390x844; sem overflow horizontal e barra
inferior fixed com bottom=844 no celular. Originais preservados sem edição.


## 2026-11-22 — login Google obrigatório para agendar

### O que mudou e por quê

A agenda era um painel local aberto. Agora /agendamento.php e os endpoints de
cadastro, edição, exclusão e situação passam por exigirLogin(), no PHP, antes
de consultar o banco. Esconder o formulário sozinho não impediria pedidos diretos.
As páginas públicas de serviços e galeria continuam acessíveis. O serviço escolhido
é preservado ao ir para a entrada e após o retorno do Google.

- src/login.php concentra sessão, configuração Google, entrada, retorno e permissões.
  Os nomes de funções e a organização seguem o PHP simples usado no projeto.
- login.php apresenta o botão e google-retorno.php recebe o código. sair.php aceita
  somente POST com token do formulário e destrói a sessão local.
- O fluxo de autorização usa state, nonce e PKCE/S256. O código é trocado diretamente
  com Google por HTTPS autenticado com o segredo. O ID token só é decodificado nessa
  resposta direta; não existe endpoint que aceite um ID token enviado pelo navegador.
  Origem, audiência, prazo, apresentador, nonce, e-mail verificado e sub são conferidos.
  Essa garantia da troca direta segue o server flow documentado pelo Google:
  https://developers.google.com/identity/openid-connect/openid-connect
- A sessão ganha novo identificador ao entrar, cookies HttpOnly/SameSite=Lax,
  Secure quando o retorno usa HTTPS e validade de oito horas. Não são guardados
  access tokens, refresh tokens ou senhas. Os escopos são apenas openid/email/profile.
- agenda_contas associa o identificador estável Google (sub) ao atendimento. Nome
  e e-mail ficam na sessão; o vínculo no banco usa sub, não e-mail. A tabela agenda
  e seus registros anteriores não foram reescritos. Antigos registros sem vínculo
  aparecem somente para administradores, sem atribuí-los por coincidência de nome.
- Clientes veem somente seus horários, podem editar/excluir os próprios registros
  ainda abertos e não alteram situação. Nome do cliente vem da conta, não do POST.
  A lista de administradores fica no arquivo local e nunca promove o primeiro login.
- Cadastro e vínculo são gravados na mesma transação, mantendo o bloqueio de
  conflitos de horário existente. O estilo e a barra inferior foram preservados.

### Estado atual: falta configurar o Google Cloud

O login real AINDA NÃO foi validado: não há cliente OAuth nem segredo fornecidos.
Por isso o botão fica desabilitado e o cadastro pelo site permanece bloqueado,
sem entrada fictícia ou alternativa de desenvolvimento exposta no navegador.
A conta administradora solicitada foi cadastrada somente no arquivo privado.

1. Acesse https://console.cloud.google.com/ e crie/selecione um projeto para o Emilyn.
2. Em Google Auth Platform, configure Branding/Marca com o nome do aplicativo e
   seus e-mails de suporte/contato. Use público Externo e modo de teste para esta
   etapa local; adicione a conta administradora e demais contas de teste em Audience.
3. Em Clients/Clientes, crie um cliente do tipo Aplicativo da Web.
4. Em URIs de redirecionamento autorizados, informe EXATAMENTE:
   http://127.0.0.1:3000/google-retorno.php
   Este fluxo não depende de origens JavaScript, chave de API ou acesso a Calendar.
5. Baixe o JSON das credenciais e mantenha-o privado. Informe ao agente apenas o
   caminho local do arquivo; não cole o segredo no chat. Os campos client_id e
   client_secret serão configurados em config/google.local.php.
6. Abra http://127.0.0.1:3000/login.php para testar o retorno real, a conta
   administradora e um cliente diferente. Use sempre 127.0.0.1:3000 nesta etapa;
   alternar com localhost pode separar os cookies e causar falha de state.

Instruções oficiais atuais:
https://support.google.com/cloud/answer/15549257
https://developers.google.com/workspace/guides/create-credentials

config/google.example.php é o modelo sem segredos. google.local.php é ignorado
pelo Git e está com modo 640, proprietário local e grupo 82 (www-data do PHP).
Após editar, preserve essas permissões; não use 644. Não foi instalado pacote,
framework, Node no host ou SDK. O cURL já existia na imagem PHP. Nenhum container
foi recriado ou reiniciado.

### Validação realizada

- PHP sem erros nos arquivos envolvidos; login HTTP 200 e agenda protegida por 303.
- POST direto sem login bloqueado nos quatro endpoints de escrita; edição também
  protegida. POST de login com CSRF inválido: 403. Logout GET: 405.
- Testes com sessões sintéticas via CLI (sem rota pública de simulação): clientes
  A/B, criação vinculada, nome forjado ignorado, separação das listas, tentativa
  de editar/excluir registro alheio sem alteração, situação restrita ao admin,
  CSRF, edição pelo titular, visão administrativa e saída aprovados.
- Registros e sessões temporários removidos; hash do conteúdo da agenda antes/depois
  dos testes coincidiu. A sequência numérica de novos IDs pode avançar nos testes.
- Identidade válida aceita; dez casos inválidos rejeitados: emissor/audiência/azp,
  expiração/data futura, nonce, e-mail não verificado/inválido e sub vazio/extenso.
- Tela conferida no desktop, 390x844 e 320x740, sem overflow horizontal; barra
  inferior fixed no limite da janela. Console do navegador sem erros.
- A autenticação real com Google, cancelamento do consentimento e retorno entre
  domínios dependem das credenciais e ainda estão pendentes.

### Backup e reversão

Backup dos arquivos: .dev-backups/pre-login-google-20261122-103848/
Backup SQL privado: .dev-backups/pre-login-google-20261122-103848/agenda-pre-login.sql

Para reverter: preserve uma cópia do estado atual; restaure apenas os arquivos
que já existiam a partir do backup, seguindo arquivos-novos.json para arquivar os
novos arquivos de login/migração/configuração. Preserve o segredo local caso já
tenha sido preenchido. A tabela agenda_contas pode permanecer sem uso; não é
necessário apagá-la nem restaurar o banco, porque os atendimentos anteriores não
foram modificados. A reversão volta ao painel local sem autenticação anterior.

O SQL de backup é para recuperação supervisionada: não importar por cima da
tabela existente. Para repetir apenas os testes de identidade, execute o arquivo
validacoes/test_claims.php do backup no container PHP. O teste de integração
validacoes/test_integration.py cria e limpa somente dados sintéticos.


## 2026-11-22 — mais fotos nos cartões e na galeria

Seis imagens dos carrosséis já revisados foram baixadas após conferir novamente
o quadro e a legenda. A revisão anterior registrava URLs, não os arquivos das
158 imagens. Antes desta etapa havia dez fotos publicadas e um story extra de
baixa resolução com enquete, mantido apenas como referência.

Novas imagens:
- Escova modelada/corte em ruivo: C-7k-A6u7vA, quadro 1.
- Mechas em cabelo castanho: C9CwzQPuDES, quadro 1.
- Corte e coloração curtos: C84RG1nuz7P, quadro 1.
- Corte chanel: C_StMl2JXR1, quadro 1 (resultado, não o antes).
- Manicure branca em atendimento: CNH30nyDylA, quadro 2.
- Manicure cinza/prata com duas mãos: DOOORddjS_p, quadro 2.

Como funciona no código:
1. src/galeria.php recebeu seis entradas no começo da lista: caminho, título,
   descrição, texto alternativo, origem, serviços e dimensões. A galeria agora
   tem 16 fotos únicas; os mesmos filtros continuam funcionando no PHP.
2. src/fotos-servicos.php liga as novas fotos às categorias Cabelo/Unhas e aos
   serviços Corte, Escova, Luzes, Coloração e Manicure. Cada cartão possui uma
   imagem de capa; as demais continuam disponíveis na galeria, sem slideshow.
3. O cartão extra Produtos dentro da lista de Cabelo tinha ícone fixo e ignorava
   o mapa de fotos. servicos.php agora reutiliza a foto de produto já existente,
   mantendo o desenho como fallback quando não houver imagem.

As fotos anteriores foram preservadas na galeria. Os arquivos originais não
foram recortados ou recomprimidos; object-fit ajusta somente a exibição e a
ampliação abre o original. As seis imagens novas são distintas por SHA-256.
Origens, quadro do carrossel, dimensões e hashes em referencias/instagram/catalogo.json.
Pedicure, massagem corporal e depilação continuam sem fotos não confirmadas.

Validação: PHP válido nos três arquivos; seis JPGs íntegros conferidos por hash
via HTTP; páginas e filtros respondendo 200, 16 imagens em Todas e três em Manicure.
Cartões de cabelo carregados no navegador, incluindo Produtos; destaque de
Manicure conferido. No celular 390x844, sem overflow horizontal e barra inferior
fixed em bottom=844. Ampliação da nova manicure e Escape funcionando; console
sem erros. Sem mudanças de CSS, login, banco ou containers.

Backup: .dev-backups/pre-mais-fotos-20261122-105550/
Reversão: restaure os três arquivos PHP e o catálogo JSON desse backup; arquive
os novos JPGs listados em arquivos-novos.json caso não sejam mais usados. Não
remova as fotos antigas. Nenhuma restauração de banco é necessária.


### Calendário mensal e horários — 22/11/2026

Abra http://127.0.0.1:3000/servico.php?id=corte e escolha “Escolher data e horário”. O calendário também aparece no formulário de cadastro/edição da agenda. Ao escolher o dia, a consulta ao banco traz apenas os intervalos ocupados do tipo correspondente: Cabeleireira ou Manicure. As categorias restantes continuam dependendo do enquadramento nesses dois tipos, conforme o modelo atual.

Expediente solicitado: 09:00–18:00, espaçamento de 1h. Duração inicial de 1h e opções até 4h; não representa duração oficial cadastrada para cada serviço. Horários que ultrapassem o expediente não aparecem. Datas passadas ficam bloqueadas na seleção nova; edição histórica mantém navegação de datas. Dias de folga e feriados ainda não estão modelados; todos os dias são consultáveis. O PHP valida os limites de horário também para gravações manuais. Registros antigos fora desse expediente continuam armazenados, mas precisam ser ajustados ao novo limite quando editados.

Arquivos principais: src/includes/calendario.php (componente), js/calendario.js (calendário e consulta), css/calendario.css (visual) e disponibilidade.php (API pública de intervalos mínimos). Os caminhos são relativos a public/. Regras de expediente estão em js/calendario.js e src/agenda.php; mantenha ambas sincronizadas se o expediente mudar.

O botão só permite continuar depois de uma consulta bem-sucedida e seleção sem conflito. A consulta visual é uma fotografia da disponibilidade; verificarConflito e o lock do MariaDB continuam protegendo o POST contra concorrência. A seleção é conservada em agendaSelecao durante a autenticação e consumida no formulário; não equivale a reserva. O login Google permanece pendente de configuração, portanto a confirmação real por cliente não foi validada.

Backup: .dev-backups/pre-calendario-20261122-110352. Para reversão, restaure individualmente os arquivos originais desse backup e arquive fora de public/ os quatro novos arquivos listados em novos-arquivos.txt; restaure também os dois documentos originais se não houver edições posteriores. Não é necessário alterar Docker ou banco de dados. Não use limpeza de volumes.

As validações PHP usam tabela temporária, e a renderização usa sessão sintética descartável. A única fixture real de interface (23/11, 12h–13h) foi removida e sua remoção confirmada no navegador. Os scripts usados estão em validacoes/ dentro do backup; o teste HTTP com ocupação exige recriar sua fixture antes de repetir e removê-la depois.


### Atualização do fluxo: atendimentos de 1 hora

Esta etapa substitui as opções anteriores de duração variável: todos os novos intervalos e edições devem durar exatamente 1 hora. O término é calculado ao escolher o início e validado também no PHP. Registros antigos não são regravados automaticamente.

O componente alterna entre mês e horários no mesmo espaço, com transição de 320ms e botão Voltar aos dias. CSS respeita prefers-reduced-motion; JavaScript usa inert no painel inativo. O formulário sem JavaScript continua aceitando preenchimento manual, com validação no servidor.

As páginas de serviço enviam o tipo oculto; tipoAgendaServico no catálogo centraliza a regra dos dois grupos já existentes: unhas → Manicure; demais categorias → Cabeleireira. Isso mantém o modelo atual de disponibilidade, sem adicionar especialidades ou profissionais. A entrada direta pela agenda continua mostrando o seletor de tipo; ao vir de um serviço, o seletor fica oculto também após login e em reapresentação de erro.

Validação desta etapa sem gravar reservas: teste com sessão sintética, validação de sintaxe e navegação real no navegador. Backup e reversão: .dev-backups/pre-fluxo-horarios-20261122-111524, com originais e teste PHP. O login Google real continua pendente de configuração.


### Google OAuth local configurado — 22/11/2026

O cliente Web já foi criado e o PHP lê suas credenciais privadas. Entre por http://127.0.0.1:3000/login.php; use esse endereço exato, pois localhost e a porta 8000 não são o retorno autorizado. O aplicativo permanece em teste. A tela Google solicita somente identidade básica: nome, foto e e-mail. A conclusão do primeiro consentimento e o retorno autenticado ainda precisam ser confirmados. Nenhum faturamento foi ativado.

Para reverter somente as credenciais, restaure config/google.local.php do backup .dev-backups/pre-credenciais-google-20261122-112922/, preservando proprietário e permissão 640/grupo do PHP. Isso desabilita o botão até nova configuração; não modifica agendamentos. Nunca publique o arquivo privado ou copie segredos para Git/documentação.
