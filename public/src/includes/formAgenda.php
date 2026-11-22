<form class="agenda-form" action="<?= $editando ? '/atualizarAgenda.php' : '/agendar.php' ?>" method="post" data-agenda-form>
    <?= tokenAgenda() ?>
    <?php if ($editando): ?><input type="hidden" name="id" value="<?= (int) $agendaDados['id'] ?>"><?php endif; ?>
    <div class="agenda-field agenda-wide"><label for="cliente">Cliente</label><input type="text" name="cliente" id="cliente" maxlength="100" required value="<?= escapeHtml($agendaDados['cliente'] ?? '') ?>" autocomplete="name"<?= !administradorAgenda() ? ' readonly' : '' ?>></div>
    <?php $servicoEscolhido = $servicoId ?? ($agendaDados['servico'] ?? '');
    $tipoFixo = $editando || !empty($veioDoServico); ?>
    <?php if ($tipoFixo): ?>
        <input type="hidden" name="tipo" value="<?= escapeHtml($editando ? $agendaDados['tipo'] : tipoAgendaServico($services[$servicoEscolhido])) ?>">
        <?php if (isset($services[$servicoEscolhido])): ?><input type="hidden" name="servico" value="<?= escapeHtml($servicoEscolhido) ?>"><?php endif; ?>
    <?php else: ?>
        <div class="agenda-field">
            <label for="servico">Tipo de atendimento</label>
            <select name="servico" id="servico" required>
                <option value="">Escolha um serviço</option>
                <?php foreach ($services as $idServico => $opcao): ?>
                    <?php if ($opcao['category'] === 'produtos') continue; ?>
                    <option value="<?= escapeHtml($idServico) ?>" data-tipo="<?= escapeHtml(tipoAgendaServico($opcao)) ?>"<?= $servicoEscolhido === $idServico ? ' selected' : '' ?>><?= escapeHtml($opcao['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="tipo" value="<?= isset($services[$servicoEscolhido]) ? escapeHtml(tipoAgendaServico($services[$servicoEscolhido])) : '' ?>">
        </div>
    <?php endif; ?>
    <?php if (!empty($veioDoServico)): ?><input type="hidden" name="origemServico" value="1"><?php endif; ?>
    <?php require __DIR__ . '/calendario.php'; ?>
    <div class="agenda-field agenda-wide"><label for="obs">Observações</label><textarea id="obs" name="obs" maxlength="500" rows="3"><?= escapeHtml($agendaDados['obs'] ?? '') ?></textarea></div>
    <div class="agenda-wide agenda-actions"><button class="button" type="submit"><?= $editando ? 'Atualizar agendamento' : 'Cadastrar agendamento' ?></button><?php if ($editando): ?><a class="text-link" href="/agendamento.php">Cancelar edição</a><?php endif; ?></div>
</form>
