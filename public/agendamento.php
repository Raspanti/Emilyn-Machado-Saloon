<?php
require __DIR__ . '/src/agenda.php';
$admin = administradorAgenda();
$pageTitle = $admin ? 'Agenda' : 'Meus agendamentos';
$agendaMensagem = $_SESSION['agendaMensagem'] ?? '';
$agendaErro = $_SESSION['agendaErro'] ?? '';
$agendaDados = $_SESSION['agendaDados'] ?? [];
unset($_SESSION['agendaMensagem'], $_SESSION['agendaErro'], $_SESSION['agendaDados']);
$clientePesquisa = $admin ? campoAgenda($_GET, 'barraPesquisa') : '';
$tipoPesquisa = $admin ? campoAgenda($_GET, 'tipoPesquisa') : '';
$situPesquisa = $admin ? campoAgenda($_GET, 'situPesquisa') : '';
$dataPesquisa = $admin ? campoAgenda($_GET, 'dataPesquisa') : '';
$agendamentos = [];
$bancoOK = true;
try {
    if ($tipoPesquisa !== '' && !in_array($tipoPesquisa, $tiposAgenda, true)) throw new DomainException('Filtro de tipo inválido.');
    if ($situPesquisa !== '' && !in_array($situPesquisa, $situacoesAgenda, true)) throw new DomainException('Filtro de situação inválido.');
    if ($dataPesquisa !== '') {
        $dataValida = DateTimeImmutable::createFromFormat('!Y-m-d', $dataPesquisa);
        if (!$dataValida || $dataValida->format('Y-m-d') !== $dataPesquisa) throw new DomainException('Filtro de data inválido.');
    }
    require __DIR__ . '/src/bdconnecta.php';
    $clienteLike = '%' . $clientePesquisa . '%';
    $sql = 'SELECT id,cliente,data,hora,horaFim,tipo,obs,situacao FROM agenda WHERE cliente LIKE ?';
    $parametros = [$clienteLike];
    if (!$admin) {
        $sql .= ' AND usuario_id=?';
        $parametros[] = usuarioLogado()['id'];
    }
    if ($tipoPesquisa !== '') { $sql .= ' AND tipo=?'; $parametros[] = $tipoPesquisa; }
    if ($situPesquisa !== '') { $sql .= ' AND situacao=?'; $parametros[] = $situPesquisa; }
    if ($dataPesquisa !== '') { $sql .= ' AND data=?'; $parametros[] = $dataPesquisa; }
    $sql .= ' ORDER BY data,hora,id LIMIT 200';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($parametros)), ...$parametros);
    mysqli_stmt_execute($stmt);
    $agendamentos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} catch (DomainException $exception) {
    $agendaErro = $exception->getMessage();
} catch (Throwable $exception) {
    $bancoOK = false;
    error_log('Agenda: ' . $exception->getMessage());
    $agendaErro = 'A agenda está indisponível. Verifique a configuração do banco local.';
}
$selecao = $_SESSION['agendaSelecao'] ?? [];
unset($_SESSION['agendaSelecao']);
if (!$agendaDados) {
    foreach (['data', 'hora', 'horaFim', 'tipo'] as $campo) {
        $valor = campoAgenda($_GET, $campo) ?: campoAgenda($selecao, $campo);
        if ($valor !== '') $agendaDados[$campo] = $valor;
    }
}
$servicoId = campoAgenda($_GET, 'servico') ?: (campoAgenda($agendaDados, 'servico') ?: campoAgenda($selecao, 'servico'));
$veioDoServico = array_key_exists('origemServico', $agendaDados)
    ? campoAgenda($agendaDados, 'origemServico') === '1'
    : isset($services[$servicoId]);
if ($veioDoServico && empty($agendaDados['obs']) && isset($services[$servicoId])) {
    $servico = $services[$servicoId];
    $agendaDados['obs'] = 'Serviço: ' . $servico['name'];
    $agendaDados['tipo'] = tipoAgendaServico($servico);
}
if (!administradorAgenda()) $agendaDados['cliente'] = usuarioLogado()['nome'];
$mostrarFormulario = campoAgenda($_GET, 'novo') === '1'
    || isset($services[$servicoId])
    || isset($agendaDados['data']);
$editando = false;
require __DIR__ . '/src/includes/header.php';
?>
<main id="conteudo" class="page-main container inner-page agenda-page">
    <header class="page-heading"><span class="eyebrow"><?= administradorAgenda() ? 'Painel do salão' : 'Sua conta' ?></span><h1><?= administradorAgenda() ? 'Agenda' : 'Meus agendamentos' ?><span class="title-dot">.</span></h1><p>Cadastre e acompanhe <?= administradorAgenda() ? 'os atendimentos de manicure e cabeleireira' : 'seus horários no salão' ?>.</p></header>
    <div class="agenda-account"><p>Conectado como <strong><?= escapeHtml(usuarioLogado()['nome']) ?></strong><br><span><?= escapeHtml(usuarioLogado()['email']) ?></span></p><form method="post" action="/sair.php"><input type="hidden" name="loginToken" value="<?= escapeHtml($_SESSION['loginToken']) ?>"><button type="submit" class="agenda-small-button">Sair</button></form></div>
    <?php if ($agendaMensagem): ?><p class="agenda-notice" role="status"><?= escapeHtml($agendaMensagem) ?></p><?php endif; ?>
    <?php if ($agendaErro): ?><p class="agenda-error agenda-notice" role="alert"><?= escapeHtml($agendaErro) ?></p><?php endif; ?>
    <?php if ($bancoOK): ?>
    <?php if ($mostrarFormulario): ?>
        <section class="agenda-panel" aria-labelledby="novo-titulo">
            <h2 id="novo-titulo">Novo agendamento</h2>
            <?php require __DIR__ . '/src/includes/formAgenda.php'; ?>
            <a class="text-link" href="/agendamento.php">Voltar aos agendamentos</a>
        </section>
    <?php else: ?>
    <section class="agenda-panel"<?= $admin ? ' aria-labelledby="lista-titulo"' : ' aria-label="Seus horários"' ?>><?php if ($admin): ?>
        <h2 id="lista-titulo">Atendimentos</h2>
        <form class="agenda-filters" method="get" action="/agendamento.php">
            <div class="agenda-field"><label for="barraPesquisa">Cliente</label><input id="barraPesquisa" name="barraPesquisa" value="<?= escapeHtml($clientePesquisa) ?>" maxlength="50"></div>
            <div class="agenda-field"><label for="tipoPesquisa">Tipo</label><select id="tipoPesquisa" name="tipoPesquisa"><option value="">Todos</option><?php foreach ($tiposAgenda as $tipo): ?><option<?= $tipoPesquisa === $tipo ? ' selected' : '' ?>><?= escapeHtml($tipo) ?></option><?php endforeach; ?></select></div>
            <div class="agenda-field"><label for="situPesquisa">Situação</label><select id="situPesquisa" name="situPesquisa"><option value="">Todas</option><?php foreach ($situacoesAgenda as $situ): ?><option<?= $situPesquisa === $situ ? ' selected' : '' ?>><?= escapeHtml($situ) ?></option><?php endforeach; ?></select></div>
            <div class="agenda-field"><label for="dataPesquisa">Data</label><input type="date" id="dataPesquisa" name="dataPesquisa" value="<?= escapeHtml($dataPesquisa) ?>"></div>
            <div class="agenda-actions"><button class="button" type="submit">Pesquisar</button><a href="/agendamento.php" class="text-link">Limpar</a></div>
        </form>
        <p class="agenda-count"><?= count($agendamentos) ?> atendimento(s) · até 200 resultados</p>
        <?php endif; ?>
        <div class="agenda-list">
        <?php foreach ($agendamentos as $agenda): ?>
            <article class="agenda-item"><div class="agenda-item-heading"><h3><?= escapeHtml($agenda[$admin ? 'cliente' : 'tipo']) ?></h3><span class="agenda-status"><?= escapeHtml($agenda['situacao']) ?></span></div><p><strong><?= escapeHtml(date('d/m/Y', strtotime($agenda['data']))) ?></strong> · <?= escapeHtml(substr($agenda['hora'], 0, 5)) ?>–<?= escapeHtml(substr($agenda['horaFim'], 0, 5)) ?><?php if ($admin): ?> · <?= escapeHtml($agenda['tipo']) ?><?php endif; ?></p><?php if ($agenda['obs'] !== ''): ?><p class="agenda-obs"><?= escapeHtml($agenda['obs']) ?></p><?php endif; ?>
                <?php if (administradorAgenda() || $agenda['situacao'] === 'Aberto'): ?><div class="agenda-item-actions"><a class="text-link" href="/editarAgenda.php?id=<?= (int) $agenda['id'] ?>">Editar</a><?php if (administradorAgenda()): ?><form method="post" action="/situacaoAgenda.php"><?= tokenAgenda() ?><input type="hidden" name="idSitu" value="<?= (int) $agenda['id'] ?>"><label class="sr-only" for="situ-<?= (int) $agenda['id'] ?>">Situação de <?= escapeHtml($agenda['cliente']) ?></label><select id="situ-<?= (int) $agenda['id'] ?>" name="situAlterada"><?php foreach ($situacoesAgenda as $situ): ?><option<?= $agenda['situacao'] === $situ ? ' selected' : '' ?>><?= escapeHtml($situ) ?></option><?php endforeach; ?></select><button type="submit" class="agenda-small-button">Salvar situação</button></form><?php endif; ?><form method="post" action="/deleteAgenda.php" data-agenda-delete><?= tokenAgenda() ?><input type="hidden" name="idDel" value="<?= (int) $agenda['id'] ?>"><button type="submit" class="agenda-delete">Excluir</button></form></div><?php endif; ?>
            </article>
        <?php endforeach; ?>
        <?php if (!$agendamentos): ?><p>Nenhum atendimento encontrado.</p><?php endif; ?>
        </div>
    </section>
    <a class="button" href="/agendamento.php?novo=1"><span aria-hidden="true">+</span> Nova agenda</a>
    <?php endif; ?>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/src/includes/footer.php'; ?>
