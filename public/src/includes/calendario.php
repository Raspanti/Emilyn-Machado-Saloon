<?php // Compartilhado pela página do serviço e pelo formulário autenticado.
$calDados = $agendaDados ?? [];
?>
<div class="booking-calendar agenda-wide" data-booking-calendar data-edit-id="<?= !empty($editando) ? (int) ($calDados['id'] ?? 0) : 0 ?>">
    <div class="booking-summary"><div><span class="eyebrow">Seu próximo cuidado</span><h2>Escolha seu dia e horário</h2></div><button type="button" class="calendar-clear">Limpar seleção</button></div>
    <input type="hidden" name="data" value="<?= escapeHtml($calDados['data'] ?? '') ?>" data-booking-date>
    <input type="hidden" name="hora" value="<?= escapeHtml(substr($calDados['hora'] ?? '', 0, 5)) ?>" data-booking-start>
    <input type="hidden" name="horaFim" value="<?= escapeHtml(substr($calDados['horaFim'] ?? '', 0, 5)) ?>" data-booking-end>
    <p class="booking-selection" role="status" data-booking-selection hidden></p>
    <div class="booking-stage">
    <div class="booking-pane booking-date-pane" data-date-pane>
    <div class="calendar-navigation"><button type="button" data-month-prev aria-label="Mês anterior">‹</button><h3 data-month-title></h3><button type="button" data-month-next aria-label="Próximo mês">›</button></div>
    <div class="calendar-weekdays" aria-hidden="true"><span>D</span><span>S</span><span>T</span><span>Q</span><span>Q</span><span>S</span><span>S</span></div>
    <div class="calendar-days" role="group" aria-label="Dias do mês" data-calendar-days></div>
    </div>
    <div class="booking-pane booking-time-pane" data-time-pane>
    <div class="booking-slots-heading"><button type="button" class="calendar-back" data-back-days>‹ Voltar aos dias</button><h3 data-slots-title tabindex="-1">Escolha um dia no calendário</h3></div>
    <p class="booking-legend"><span>○ Disponível</span><span class="booking-busy-key">● Ocupado · indisponível</span></p>
    <p class="booking-feedback" role="status" aria-live="polite" data-booking-feedback>Selecione uma data para consultar a disponibilidade.</p>
    <div class="booking-slots" role="group" aria-label="Horários de início" data-booking-slots></div>
    </div>
    </div>
    <small class="booking-help">Cada atendimento dura 1 hora. Escolha o dia e depois o horário. A disponibilidade será conferida novamente ao confirmar.</small>
    <noscript><p>Ative o JavaScript para escolher o dia e o horário no calendário.</p></noscript>
</div>
