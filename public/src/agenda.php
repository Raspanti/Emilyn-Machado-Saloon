<?php
require_once __DIR__ . '/bootstrap.php';
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__ . '/login.php';
iniciarSessaoLogin();
exigirLogin();
if (empty($_SESSION['agendaToken'])) $_SESSION['agendaToken'] = bin2hex(random_bytes(32));
$agendaPage = true;
$tiposAgenda = ['Manicure', 'Cabeleireira'];
$situacoesAgenda = ['Aberto', 'Atendido', 'Faltoso'];

function campoAgenda(array $campos, string $nome): string
{
    return isset($campos[$nome]) && is_string($campos[$nome]) ? trim($campos[$nome]) : '';
}

function tokenAgenda(): string
{
    return '<input type="hidden" name="agendaToken" value="' . escapeHtml($_SESSION['agendaToken']) . '">';
}

function voltarAgenda(string $mensagem, string $erro = '', array $dados = [], int $id = 0): never
{
    $_SESSION['agendaMensagem'] = $mensagem;
    $_SESSION['agendaErro'] = $erro;
    $_SESSION['agendaDados'] = $dados;
    header('Location: ' . ($id > 0 ? '/editarAgenda.php?id=' . $id : '/agendamento.php'), true, 303);
    exit;
}

function validarAgenda(array $campos): array
{
    global $tiposAgenda, $services;
    $dados = [];
    foreach (['cliente', 'data', 'hora', 'horaFim', 'tipo', 'obs', 'servico'] as $campo) $dados[$campo] = campoAgenda($campos, $campo);
    if ($dados['cliente'] === '' || mb_strlen($dados['cliente']) > 50) throw new DomainException('Informe um cliente com até 50 caracteres.');
    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $dados['data']);
    if (!$data || $data->format('Y-m-d') !== $dados['data']) throw new DomainException('Informe uma data válida.');
    foreach (['hora', 'horaFim'] as $campo) {
        if (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D', $dados[$campo])) throw new DomainException('Informe horários válidos.');
    }
    if ($dados['horaFim'] <= $dados['hora']) throw new DomainException('O horário final precisa ser posterior ao inicial.');
    if ($dados['hora'] < '09:00' || $dados['horaFim'] > '18:00'
        || substr($dados['hora'], 3) !== '00' || substr($dados['horaFim'], 3) !== '00') {
        throw new DomainException('O salão atende das 9h às 18h, com horários de hora em hora.');
    }
    if ((strtotime($dados['horaFim']) - strtotime($dados['hora'])) !== 3600) throw new DomainException('Cada atendimento deve durar exatamente 1 hora.');
    if (isset($services[$dados['servico']])) $dados['tipo'] = tipoAgendaServico($services[$dados['servico']]);
    if (!in_array($dados['tipo'], $tiposAgenda, true)) throw new DomainException('Selecione um tipo de atendimento válido.');
    if (mb_strlen($dados['obs']) > 500) throw new DomainException('As observações devem ter até 500 caracteres.');
    return $dados;
}

function idAgenda(array $campos, string $nome): int
{
    $id = filter_var(campoAgenda($campos, $nome), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id) throw new DomainException('Agendamento inválido.');
    return $id;
}

function buscarAgenda(mysqli $conn, int $id): array
{
    $sql = 'SELECT id, cliente, data, hora, horaFim, tipo, obs, situacao FROM agenda WHERE id=?';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $agenda = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$agenda) throw new DomainException('Agendamento não encontrado.');
    if (!administradorAgenda()) {
        $sub = usuarioLogado()['sub'];
        $dono = mysqli_prepare($conn, 'SELECT agenda_id FROM agenda_contas WHERE agenda_id=? AND google_sub=?');
        mysqli_stmt_bind_param($dono, 'is', $id, $sub);
        mysqli_stmt_execute($dono);
        $permitido = mysqli_fetch_assoc(mysqli_stmt_get_result($dono));
        mysqli_stmt_close($dono);
        if (!$permitido) throw new DomainException('Agendamento não encontrado.');
    }
    return $agenda;
}

function verificarConflito(mysqli $conn, array $dados, int $id = 0): void
{
    $sql = "SELECT id FROM agenda WHERE tipo=? AND data=? AND situacao='Aberto' AND hora < ? AND horaFim > ? AND id != ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssi', $dados['tipo'], $dados['data'], $dados['horaFim'], $dados['hora'], $id);
    mysqli_stmt_execute($stmt);
    $conflito = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($conflito) throw new DomainException('Já existe atendimento aberto desse tipo no intervalo escolhido.');
}

function executarAcaoAgenda(string $acao): never
{
    global $services;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Esta ação aceita somente POST.');
    }
    if (!hash_equals($_SESSION['agendaToken'], campoAgenda($_POST, 'agendaToken'))) {
        http_response_code(403);
        exit('Formulário expirado. Reabra a agenda e tente novamente.');
    }
    $conn = null;
    $bloqueado = false;
    $id = 0;
    $erro = '';
    $dados = [];
    $transacao = false;
    try {
        if ($acao === 'situacao' && !administradorAgenda()) {
            http_response_code(403);
            exit('Somente o salão pode alterar a situação.');
        }
        if ($acao === 'criar' || $acao === 'atualizar') {
            foreach (['cliente','data','hora','horaFim','tipo','obs','servico'] as $campo) $dados[$campo] = campoAgenda($_POST, $campo);
            if ($acao === 'atualizar') $id = idAgenda($_POST, 'id');
            $campos = $_POST;
            if (!administradorAgenda()) $campos['cliente'] = usuarioLogado()['nome'];
            $origemServico = campoAgenda($_POST, 'origemServico');
            $dados['origemServico'] = $origemServico;
            if ($acao === 'criar' && $origemServico !== '1') {
                $opcao = $services[$dados['servico']] ?? null;
                if (!$opcao || $opcao['category'] === 'produtos') throw new DomainException('Escolha um serviço.');
                $campos['obs'] = 'Serviço: ' . $opcao['name'] . ($dados['obs'] !== '' ? "\n" . $dados['obs'] : '');
            }
            $dados = validarAgenda($campos);
            $dados['origemServico'] = $origemServico;
        } else {
            $id = idAgenda($_POST, $acao === 'excluir' ? 'idDel' : 'idSitu');
        }
        require __DIR__ . '/bdconnecta.php';
        // Serializa cadastro/edição/situação para dois pedidos não ocuparem o mesmo horário.
        $lock = mysqli_fetch_row(mysqli_query($conn, "SELECT GET_LOCK(CONCAT(DATABASE(), ':agenda'), 5)"));
        if ((int) $lock[0] !== 1) throw new DomainException('A agenda está ocupada. Tente novamente.');
        $bloqueado = true;
        mysqli_begin_transaction($conn);
        $transacao = true;
        if ($acao === 'criar') {
            verificarConflito($conn, $dados);
            $DTHR = date('Y-m-d H:i:s');
            $sql = 'INSERT INTO agenda (cliente,data,hora,horaFim,DTHR,tipo,obs) VALUES (?,?,?,?,?,?,?)';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssss', $dados['cliente'], $dados['data'], $dados['hora'], $dados['horaFim'], $DTHR, $dados['tipo'], $dados['obs']);
        } elseif ($acao === 'atualizar') {
            $atual = buscarAgenda($conn, $id);
            if (!administradorAgenda() && $atual['situacao'] !== 'Aberto') throw new DomainException('Este atendimento não pode mais ser alterado.');
            if ($atual['situacao'] === 'Aberto') verificarConflito($conn, $dados, $id);
            $sql = 'UPDATE agenda SET cliente=?,data=?,hora=?,horaFim=?,tipo=?,obs=? WHERE id=?';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssssi', $dados['cliente'], $dados['data'], $dados['hora'], $dados['horaFim'], $dados['tipo'], $dados['obs'], $id);
        } elseif ($acao === 'situacao') {
            global $situacoesAgenda;
            $situ = campoAgenda($_POST, 'situAlterada');
            if (!in_array($situ, $situacoesAgenda, true)) throw new DomainException('Situação inválida.');
            $atual = buscarAgenda($conn, $id);
            if ($situ === 'Aberto') verificarConflito($conn, $atual, $id);
            $stmt = mysqli_prepare($conn, 'UPDATE agenda SET situacao=? WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'si', $situ, $id);
        } else {
            $atual = buscarAgenda($conn, $id);
            if (!administradorAgenda() && $atual['situacao'] !== 'Aberto') throw new DomainException('Este atendimento não pode mais ser excluído.');
            $stmt = mysqli_prepare($conn, 'DELETE FROM agenda WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($acao === 'criar') {
            $novoId = mysqli_insert_id($conn);
            $sub = usuarioLogado()['sub'];
            $vinculo = mysqli_prepare($conn, 'INSERT INTO agenda_contas (agenda_id,google_sub) VALUES (?,?)');
            mysqli_stmt_bind_param($vinculo, 'is', $novoId, $sub);
            mysqli_stmt_execute($vinculo);
            mysqli_stmt_close($vinculo);
        }
        mysqli_commit($conn);
        $transacao = false;
    } catch (DomainException $exception) {
        $erro = $exception->getMessage();
    } catch (Throwable $exception) {
        error_log('Agenda: ' . $exception->getMessage());
        $erro = 'Não foi possível salvar. Confira a conexão e tente novamente.';
    } finally {
        if ($conn instanceof mysqli) {
            if ($transacao) mysqli_rollback($conn);
            if ($bloqueado) mysqli_query($conn, "SELECT RELEASE_LOCK(CONCAT(DATABASE(), ':agenda'))");
            mysqli_close($conn);
        }
    }
    if ($erro) voltarAgenda('', $erro, $dados, $acao === 'atualizar' ? $id : 0);
    if ($acao === 'criar' && $dados['origemServico'] === '1' && isset($services[$dados['servico']])) {
        $servicoId = $dados['servico'];
        $_SESSION['agendaServicoConfirmado'] = $servicoId;
        header('Location: /servico.php?id=' . rawurlencode($servicoId), true, 303);
        exit;
    }
    voltarAgenda($acao === 'criar' ? 'Agendamento confirmado.' : 'Agenda atualizada com sucesso.');
}
