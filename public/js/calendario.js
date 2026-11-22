// Mesma regra do servidor: intervalos adjacentes são permitidos.
function bookingMinutes(value) {
    const [hours, minutes] = value.split(':').map(Number);
    return hours * 60 + minutes;
}
function bookingTime(value) {
    return `${String(Math.floor(value / 60)).padStart(2, '0')}:${String(value % 60).padStart(2, '0')}`;
}
function bookingOverlaps(start, end, occupied) {
    return occupied.some(item => bookingMinutes(item.inicio) < end && bookingMinutes(item.fim) > start);
}

document.querySelectorAll('[data-booking-calendar]').forEach(calendar => {
    const form = calendar.closest('form');
    const dayInput = calendar.querySelector('[data-booking-date]');
    const startInput = calendar.querySelector('[data-booking-start]');
    const endInput = calendar.querySelector('[data-booking-end]');
    const datePane = calendar.querySelector('[data-date-pane]');
    const timePane = calendar.querySelector('[data-time-pane]');
    calendar.classList.add('is-enhanced');
    function showTimes(show, focus = false) {
        calendar.classList.toggle('is-slots', show);
        datePane.inert = show; timePane.inert = !show;
        if (focus) calendar.querySelector('.booking-stage').scrollIntoView({block: 'start', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'});
        if (focus) (show ? calendar.querySelector('[data-back-days]') : days.querySelector('[aria-pressed="true"]') || calendar.querySelector('[data-month-next]')).focus({preventScroll: true});
    }
    const typeInput = form.querySelector('[name="tipo"]');
    const submit = form.querySelector('[type="submit"]');
    const slots = calendar.querySelector('[data-booking-slots]');
    const selection = calendar.querySelector('[data-booking-selection]');
    const feedback = calendar.querySelector('[data-booking-feedback]');
    const days = calendar.querySelector('[data-calendar-days]');
    const editing = Number(calendar.dataset.editId) > 0;
    const today = new Intl.DateTimeFormat('en-CA', {timeZone: 'America/Sao_Paulo', year: 'numeric', month: '2-digit', day: '2-digit'}).format(new Date());
    const dateKey = date => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    function validDay(value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value) || (!editing && value < today)) return false;
        return dateKey(new Date(`${value}T12:00:00`)) === value;
    }
    if (!validDay(dayInput.value)) {
        dayInput.value = ''; startInput.value = ''; endInput.value = '';
    }
    let month = new Date(`${dayInput.value || today}T12:00:00`);
    month.setDate(1);
    let occupied = [], loaded = false, request = 0, controller;
    const opening = 9 * 60, closing = 18 * 60, step = 60;
    if (startInput.value && bookingMinutes(startInput.value) + step < 1440) {
        endInput.value = bookingTime(bookingMinutes(startInput.value) + step);
    }
    function drawMonth() {
        calendar.querySelector('[data-month-title]').textContent = new Intl.DateTimeFormat('pt-BR', {month: 'long', year: 'numeric'}).format(month);
        days.replaceChildren();
        for (let i = 0; i < month.getDay(); i++) days.append(document.createElement('span'));
        const count = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
        for (let day = 1; day <= count; day++) {
            const date = new Date(month.getFullYear(), month.getMonth(), day, 12);
            const key = dateKey(date), button = document.createElement('button');
            button.type = 'button'; button.textContent = day;
            button.setAttribute('aria-label', new Intl.DateTimeFormat('pt-BR', {dateStyle: 'full'}).format(date));
            button.setAttribute('aria-pressed', String(dayInput.value === key));
            if (key === today) button.setAttribute('aria-current', 'date');
            button.disabled = !editing && key < today;
            button.addEventListener('click', () => {
                dayInput.value = key; startInput.value = ''; endInput.value = '';
                drawMonth(); showTimes(true, true); loadAvailability();
            });
            days.append(button);
        }
        const earliestMonth = today.slice(0, 7);
        calendar.querySelector('[data-month-prev]').disabled = !editing && dateKey(month).slice(0, 7) <= earliestMonth;
    }
    function validate() {
        selection.hidden = !dayInput.value;
        const selectedDay = dayInput.value.split('-').reverse().join('/');
        selection.textContent = startInput.value && endInput.value
            ? `${selectedDay} · ${startInput.value} às ${endInput.value}`
            : selectedDay;
        let error = '';
        if (!loaded) error = 'Consulte a disponibilidade antes de continuar.';
        else if (startInput.value && endInput.value) {
            const start = bookingMinutes(startInput.value), end = bookingMinutes(endInput.value);
            if (end - start !== 60) error = 'Cada atendimento dura exatamente 1 hora.';
            else if (start < opening || end > closing || start % step || end % step) error = 'Escolha horários entre 9h e 18h, de hora em hora.';
            else if (bookingOverlaps(start, end, occupied)) error = 'Este intervalo já está ocupado. Escolha outro horário.';
        }
        submit.disabled = !loaded || !!error || !dayInput.value || !startInput.value || !endInput.value;
        if (error && loaded) feedback.textContent = error;
        if (loaded && !error) {
            feedback.textContent = [...slots.children].length && [...slots.children].every(button => button.disabled)
                    ? 'Nenhum horário disponível. Escolha outro dia.'
                    : 'Atendimento das 9h às 18h. Selecione um horário disponível.';
        }
        return !submit.disabled;
    }
    function drawSlots() {
        slots.replaceChildren();
        if (!loaded) return;
        const length = 60;
        for (let start = opening; start + length <= closing; start += step) {
            const end = start + length, busy = bookingOverlaps(start, end, occupied);
            const button = document.createElement('button');
            button.type = 'button'; button.disabled = busy;
            button.className = busy ? 'booking-slot is-busy' : 'booking-slot';
            button.setAttribute('aria-pressed', String(startInput.value === bookingTime(start) && endInput.value === bookingTime(end)));
            button.textContent = `${bookingTime(start)}${busy ? ' · Ocupado' : ''}`;
            button.setAttribute('aria-label', `${bookingTime(start)} até ${bookingTime(end)}${busy ? ', ocupado, indisponível' : ', disponível'}`);
            button.addEventListener('click', () => {
                startInput.value = bookingTime(start); endInput.value = bookingTime(end);
                drawSlots(); validate();
            });
            slots.append(button);
        }
        if (![...slots.children].some(button => !button.disabled)) feedback.textContent = 'Nenhum horário disponível. Escolha outro dia.';
    }
    async function loadAvailability() {
        const current = ++request;
        controller?.abort(); controller = new AbortController();
        loaded = false; occupied = []; slots.replaceChildren(); validate();
        if (!validDay(dayInput.value)) {
            feedback.textContent = 'Selecione uma data válida para consultar a disponibilidade.'; return;
        }
        calendar.querySelector('[data-slots-title]').textContent = new Intl.DateTimeFormat('pt-BR', {day: 'numeric', month: 'long'}).format(new Date(`${dayInput.value}T12:00:00`));
        if (!typeInput.value) {
            feedback.textContent = 'Escolha um serviço para consultar os horários.';
            return;
        }
        feedback.textContent = 'Consultando horários…';
        const params = new URLSearchParams({data: dayInput.value, tipo: typeInput.value});
        if (editing) params.set('editar', calendar.dataset.editId);
        try {
            const response = await fetch(`/disponibilidade.php?${params}`, {cache: 'no-store', signal: controller.signal});
            const data = await response.json();
            if (current !== request) return;
            if (!response.ok || !Array.isArray(data.ocupados)) throw new Error(data.erro || 'Não foi possível consultar os horários.');
            occupied = data.ocupados; loaded = true;
            feedback.textContent = 'Atendimento das 9h às 18h. Selecione um horário disponível.';
            drawSlots(); validate();
        } catch (error) {
            if (current !== request || error.name === 'AbortError') return;
            feedback.textContent = 'Não foi possível consultar os horários. ';
            const retry = document.createElement('button'); retry.type = 'button'; retry.textContent = 'Tentar novamente';
            retry.addEventListener('click', loadAvailability); feedback.append(retry);
            validate();
        }
    }
    calendar.querySelector('[data-month-prev]').addEventListener('click', () => {month.setMonth(month.getMonth() - 1); drawMonth();});
    calendar.querySelector('[data-month-next]').addEventListener('click', () => {month.setMonth(month.getMonth() + 1); drawMonth();});
    calendar.querySelector('.calendar-clear').addEventListener('click', () => {
        dayInput.value = ''; startInput.value = ''; endInput.value = '';
        calendar.querySelector('[data-slots-title]').textContent = 'Escolha um dia no calendário';
        drawMonth(); showTimes(false, true); loadAvailability();
    });
    const serviceSelect = form.querySelector('select[name="servico"]');
    serviceSelect?.addEventListener('change', () => {
        typeInput.value = serviceSelect.selectedOptions[0]?.dataset.tipo ?? '';
        typeInput.dispatchEvent(new Event('change'));
    });
    typeInput.addEventListener('change', () => {startInput.value = ''; endInput.value = ''; loadAvailability();});
    calendar.querySelector('[data-back-days]').addEventListener('click', () => showTimes(false, true));
    form.addEventListener('submit', event => {
        if (!validate()) event.preventDefault();
    });
    // Atualiza ao retornar à aba; o POST também verifica concorrência no banco.
    document.addEventListener('visibilitychange', () => {if (!document.hidden && dayInput.value) loadAvailability();});
    drawMonth(); showTimes(!!dayInput.value); loadAvailability();
});
