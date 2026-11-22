# Histórico de desenvolvimento

## 2026-11-17 — estrutura mobile do Emilyn

- Backup integral de public e arquivos auxiliares em `.dev-backups/pre-mobile-20261117-161127`.
- Estrutura PHP com documento único; eliminadas referências ao menu de adoção e a arquivos inexistentes.
- Reaproveitados os conceitos de includes, variáveis CSS e interações do MEAW, sem editar o projeto original.
- Grade de seis categorias, detalhes de serviços, sobre e contato. Catálogo e contatos separados dos templates.
- Layout mobile first, paleta sálvia dos esboços, ilustrações SVG provisórias, barra inferior e adaptação para desktop.
- Agendar encaminha ao contato com serviço selecionado; contatos aguardam dados confirmados. Sem reserva de horários ou mudanças de banco.
- Menu com estado ARIA, Escape e fechamento externo; respeito a movimento reduzido.
- PHP/JS válidos; 34 URLs sem falhas, duas respostas 404 verificadas; browser QA mobile/desktop e live reload real aprovados. Detalhes em GUIA_DESENVOLVIMENTO.md.
- Reversão disponível em tools/restaurar-pre-mobile.py, com arquivo do estado atual antes de restaurar.
- Compose, containers e volume MariaDB preservados. Nenhuma dependência de projeto adicionada.


## 2026-11-21 — retomada da versão completa

- Restaurada a versão mobile na pasta principal, sem commit ou merge.
- Backup prévio: `.dev-backups/pre-restaurar-completo-20261121-120947`.
- Nomes de funções, variáveis e classe active alinhados ao estilo observado no MEAW.
- Docker, banco e pasta de referência preservados.
- Validação desta retomada: PHP local 8.5.12 e JS sem erros; 32 renderizações válidas e 404 conferidos. Docker inativo, sem validação no container/navegador nesta etapa.


## 2026-11-21 — cache de CSS/JS após restauração

- Causa observada: CSSOM do navegador ainda continha estilos MEAW, enquanto o servidor entregava o CSS novo (hash idêntico ao arquivo atual). A restauração preservava datas antigas dos arquivos, favorecendo reutilização do cache.
- Adicionado parâmetro v com hash do conteúdo às URLs de CSS e JS. Não altera o desenho; invalida o cache quando o arquivo muda, inclusive ao restaurar backups.
- Backup: `.dev-backups/pre-cache-assets-20261121-121510`.


## 2026-11-21 — integração do painel de agendamento

- Integrado CRUD do ProjetoCabelo ao layout Emilyn, preservando nomes dos campos e endpoints.
- Corrigidos filtros, validação no servidor, escape HTML e requisições POST com token de sessão; acrescentada verificação de conflitos por tipo.
- Criada tabela agenda vazia em InnoDB/utf8mb4, com DTHR DATETIME. Clientes do ZIP não importados; conexão remota não utilizada.
- Imagem PHP com mysqli e override local; app/web recriados com autorização. Banco e volume preservados.
- .env seleciona os três Compose; credenciais locais em .env.agenda ignorado pelo Git.
- Backup de arquivos e banco em .dev-backups/pre-agendamento-20261121-122152.
- CRUD e validações testados por HTTP, registros temporários removidos; mobile/desktop e preenchimento do serviço conferidos no navegador. Detalhes no guia.


## 2026-11-21 — navegação única no celular

- Removidos botão hambúrguer e JavaScript do menu expansível, que coexistiam indevidamente com a barra inferior.
- Abaixo de 900px: marca no header e barra inferior fixa. A partir de 900px: links superiores, sem barra inferior.
- Não foi reproduzido desaparecimento da barra: na inspeção ela estava visível e alinhada ao viewport.
- Backup: `.dev-backups/pre-navegacao-mobile-20261121-123647`.


## 2026-11-21 — barra inferior na área visível

- A barra já usava fixed; a investigação não reproduziu desaparecimento por rolagem simples. Não havia tratamento da diferença entre layout viewport e visual viewport com teclado.
- Extraída a navegação para src/includes/bottom-nav.php, carregada pelo header. O footer não cria uma segunda barra.
- scripts.js calcula o recuo usando altura/offsetTop da VisualViewport, atualiza em resize/scroll/foco e reúne eventos em requestAnimationFrame. Zoom por pinça não é tratado como teclado.
- CSS reserva a altura real da barra no conteúdo e no scroll-padding; fundo sólido e z-index 1000. Meta viewport solicita resizes-content onde suportado.
- Backup: .dev-backups/pre-barra-fixa-20261121-124138/.
- PHP e JS válidos. No navegador: barra no bottom=844 no topo e após rolar 1196px; campo de observações focado sem encobrimento; bottom=500 na janela reduzida; uma única barra. Desktop sem barra nem padding extra.
- Testes simulados do JS: teclado sobreposto, viewport deslocado, fechamento, resize do layout, zoom, desktop e fallback sem VisualViewport aprovados. Teclado virtual de aparelho físico não disponível para validação nesta sessão.


## 2026-11-21 — galeria de trabalhos

- Galeria com sete fotos únicas, filtros por serviço no PHP e ampliação com dialog nativo.
- Dados em src/galeria.php, imagens locais e CSS/JS separados, carregados apenas na galeria.
- Links na home, rodapé e navegação de desktop; barra inferior preservada.
- Fontes, cores e cartões seguem o Emilyn. Miniaturas recortam apenas a exibição; originais preservados byte a byte.
- Backup integral prévio em .dev-backups/pre-galeria-20261121-182114.
- Validados filtros, imagens, 404, PHP/JS, responsividade, ampliação/Escape/foco e console. Sem mudanças no banco ou nos containers.


## 2026-11-21 — fotos nos cartões e serviços

- Aplicadas fotos da galeria a duas categorias e cinco serviços confirmados, mantendo desenhos nos demais.
- Associações em src/fotos-servicos.php reutilizam metadados e arquivos da galeria sem duplicá-los.
- Templates escolhem imagem ou ícone; CSS preserva cartões e adapta destaques verticais. Comparação de tratamento exibida inteira com nota de atendimento combinado.
- Backup em .dev-backups/pre-fotos-servicos-20261121-182703.
- Validados PHP, fotos/rotas por HTTP e layout mobile/desktop no navegador, incluindo preservação da barra inferior. Sem mudanças no banco.


## 2026-11-21 — manicure e escova confirmadas no Instagram

- Pesquisados destaque Unhas e posts do perfil após login, sem ações de mensagem, seguir ou curtir.
- Aplicada foto de manicure cinza/prata à categoria Unhas e ao serviço Manicure; foto de escova com confirmação explícita na legenda aplicada ao serviço Escova.
- Galeria ampliada de sete para nove imagens, com filtros Manicure e Escova; story azul preservado só como referência.
- Pedicure e demais serviços sem foto específica mantêm ilustrações. Sem alterações de templates, CSS, banco ou containers nesta etapa.
- Backup: .dev-backups/pre-fotos-unhas-escova-20261121-184800. Origens e validações no catálogo de referências e no guia.
- Validados PHP, imagens e rotas por HTTP e inspeção visual mobile/desktop; barra inferior preservada.


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


## 2026-11-22 — acesso Google à agenda

- Login Google preparado em PHP, com state/nonce/PKCE, sessão protegida e logout POST.
- Agenda e endpoints exigem login; clientes acessam apenas seus próprios registros.
- Administração por lista explícita no arquivo local; registros antigos restritos ao admin.
- Tabela agenda_contas criada com backup SQL; cadastro e vínculo na mesma transação.
- Configuração privada fora de public e do Git, modo 640/grupo PHP, sem SDK novo.
- Testes de PHP, isolamento, autorização, CSRF, identidade e responsividade aprovados.
- Sem credenciais Google Cloud: entrada real pendente; botão desabilitado e agenda
  bloqueada para visitantes. Passo a passo, limitações e rollback no guia.
- Backup: .dev-backups/pre-login-google-20261122-103848/. Nenhum container reiniciado ou recriado.


## 2026-11-22 — galeria e agendamento na barra móvel

- Barra inferior reorganizada: Galeria, Início, Agendar. Galeria substitui o antigo Agendar à esquerda; Agendar substitui Topo à direita.
- Adicionado ícone de galeria no helper existente. CSS e comportamento de barra fixa preservados.
- Backup: .dev-backups/pre-barra-galeria-agendar-20261122-104706/; reversão restaurando bottom-nav.php e bootstrap.php desse backup.


## 2026-11-22 — seis fotos adicionais do Instagram

- Galeria ampliada de dez para 16 fotos únicas; novas imagens exibidas primeiro.
- Novas capas nas categorias Cabelo/Unhas e nos serviços Corte, Escova, Luzes, Coloração e Manicure.
- Corrigido cartão extra Produtos em Cabelo para reutilizar a foto existente.
- Originais e respectivas fontes/hashes registrados; fotos anteriores preservadas.
- Validados PHP, HTTP/imagens, filtros, ampliação e layout mobile/desktop. Sem mudanças de CSS ou banco.
- Backup: .dev-backups/pre-mais-fotos-20261122-105550/; explicação e rollback no guia.


### 2026-11-22 — calendário de disponibilidade no serviço e na agenda

- Página de serviço agora oferece calendário mensal, seleção de dia e opções de início; campos Data/Início/Término resumem a escolha. Calendário compartilhado também no cadastro e edição.
- Expediente definido pelo usuário: 9h–18h, com início de hora em hora. Duração inicial de 1h, ajustável para 2h, 3h ou 4h; último início de 1h é 17h. PHP também valida expediente e horas inteiras.
- GET disponibilidade.php retorna somente intervalos abertos por data/tipo; não retorna nomes, observações, contas ou IDs. Horários conflitantes ficam vermelhos e desabilitados, inclusive se só parte da duração sobrepõe uma reserva. Falha de consulta bloqueia a continuação e permite tentar novamente.
- Mantidos login Google, CSRF, autorização por conta, consultas preparadas, bloqueio de concorrência e verificação de sobreposição no POST. Consulta de edição só desconsidera registro após validar login e permissão. Seleção pública é conservada em sessão ao passar pelo login e nunca cria reserva via GET.
- Sem novas dependências, migração de banco, alteração de Docker ou publicação remota. Alterações locais anteriores foram preservadas.
- Validações: sintaxe PHP/JS; 14 verificações PHP de expediente/conflitos em tabela temporária; 6 verificações JS; 7 verificações HTTP incluindo dados mínimos, tipos separados, entradas inválidas, método e edição sem login; renderização autenticada com sessão sintética confirmou seleção preservada e campos únicos.
- Navegador: dia selecionado, horário disponível, reserva sintética das 12h vermelha/desabilitada, duração de 2h bloqueando sobreposição parcial e encaminhamento ao login. Fixture ID 8 removida; navegador confirmou 12h disponível novamente. Captura do estado de teste: /home/raspanty/Documentos/ChatGPT/Sys/emilyn-calendario-validacao.jpg.
- Limite: login Google continua sem configuração; não houve autenticação Google real nem confirmação de reserva por cliente. A duração não é calculada por serviço; não há profissionais individuais, folgas ou feriados cadastrados.
- Backup anterior: .dev-backups/pre-calendario-20261122-110352. Validações e lista de arquivos novos guardadas nesse backup. A sequência de IDs avançou por causa da fixture; nenhum dado prévio foi alterado.


### 2026-11-22 — seleção de dia e horário em etapas, com duração fixa

- Removidos a pergunta de tipo e o texto de confirmação nas páginas de serviços. Manicure/pedicure selecionam automaticamente Manicure; demais serviços mantêm a agenda Cabeleireira, conforme os dois grupos existentes. O formulário após login conserva o serviço e oculta o seletor; a entrada direta na agenda mantém a escolha. Edição conserva o tipo existente.
- Duração fixa de 1h: removido o seletor, término calculado e somente leitura no navegador; PHP rejeita durações diferentes e deriva o tipo pelo serviço quando informado. Expediente 9h–18h preservado.
- Selecionar dia desliza o calendário à direita e apresenta horários no mesmo espaço; Voltar aos dias retorna ao mês escolhido. Painel inativo fica fora da navegação por teclado; preferência de movimento reduzido desativa a transição. Consulta e bloqueio de horários ocupados preservados.
- Validações aprovadas: sintaxe dos seis PHP alterados e JS, verificações JS de conflitos, teste PHP com sessão sintética para manicure automática, tipo imposto no servidor, duração fixa e entrada direta com seletor. Navegador confirmou mês → horários → voltar → outro dia, ausência de perguntas redundantes e intervalo 11h–12h.
- Sem reservas de teste nesta etapa. Login Google real permanece pendente da configuração anterior. Captura: /home/raspanty/Documentos/ChatGPT/Sys/emilyn-fluxo-horarios.jpg.
- Backup: .dev-backups/pre-fluxo-horarios-20261122-111524; restaure os arquivos correspondentes para reverter apenas esta etapa. Não exige alteração de banco ou Docker.


### 2026-11-22 — cliente Google OAuth local configurado

- Criado cliente Web Emilyn local — PHP, com retorno http://127.0.0.1:3000/google-retorno.php. Aplicativo externo em modo Testando; conta administrativa adicionada aos usuários de teste. Nenhum faturamento ativado.
- Credenciais armazenadas somente em config/google.local.php, ignorado pelo Git, com permissão 640 e grupo do PHP. Backup privado anterior em .dev-backups/pre-credenciais-google-20261122-112922/.
- PHP confirmou configuração válida; botão do site abriu seleção de conta Google e consentimento apenas para nome/foto/e-mail. Confirmação do consentimento e retorno autenticado ainda pendentes. Sem alterações em Docker, banco ou calendário nesta etapa.
