<?php
$categories = [
    'cabelo' => ['name' => 'Cabelo', 'icon' => 'hair', 'intro' => 'Um novo corte, uma nova cor, o seu jeito de ser.'],
    'sobrancelha' => ['name' => 'Sobrancelha', 'icon' => 'eye', 'intro' => 'Detalhes que valorizam a expressão do seu olhar.'],
    'unhas' => ['name' => 'Unhas', 'icon' => 'nails', 'intro' => 'Um momento de cuidado, da escolha da cor ao acabamento.'],
    'massagem' => ['name' => 'Massagem', 'icon' => 'leaf', 'intro' => 'Uma pausa na rotina para cuidar de você.'],
    'depilacao' => ['name' => 'Depilação', 'icon' => 'spark', 'intro' => 'Conheça as opções de cuidado para a sua pele.'],
    'produtos' => ['name' => 'Produtos', 'icon' => 'bottle', 'intro' => 'O cuidado continua na sua rotina.'],
];
$services = [
    'corte' => ['category' => 'cabelo', 'name' => 'Corte', 'description' => 'Converse sobre o comprimento, o movimento e o estilo que fazem sentido para você. A proposta é encontrar um corte que acompanhe a sua rotina.'],
    'tratamento-capilar' => ['category' => 'cabelo', 'name' => 'Tratamento capilar', 'description' => 'Um cuidado pensado a partir das necessidades dos seus fios. Consulte o salão para conhecer os tratamentos disponíveis.'],
    'escova' => ['category' => 'cabelo', 'name' => 'Escova', 'description' => 'Finalização para realçar o movimento dos fios, seja para o dia a dia ou uma ocasião especial.'],
    'luzes' => ['category' => 'cabelo', 'name' => 'Luzes', 'description' => 'Ilumine os fios com uma proposta de cor que combine com você. Antes do procedimento, converse com a profissional sobre o resultado desejado e os cuidados necessários.'],
    'coloracao' => ['category' => 'cabelo', 'name' => 'Coloração', 'description' => 'Da renovação da cor a uma mudança de visual. A escolha do tom e do procedimento deve ser feita em conversa com a profissional.'],
    'design' => ['category' => 'sobrancelha', 'name' => 'Design de sobrancelhas', 'description' => 'Um desenho que respeita as características do seu rosto. Consulte as opções e encontre o cuidado mais adequado para você.'],
    'manicure' => ['category' => 'unhas', 'name' => 'Manicure', 'description' => 'Escolha a sua cor e reserve um momento para cuidar das mãos. Consulte as opções de esmaltação disponíveis.'],
    'pedicure' => ['category' => 'unhas', 'name' => 'Pedicure', 'description' => 'Cuidado com os pés e acabamento das unhas. Converse com a equipe para saber mais sobre o atendimento.'],
    'massagem-relaxante' => ['category' => 'massagem', 'name' => 'Massagem relaxante', 'description' => 'Uma pausa dedicada ao seu bem-estar. Consulte a disponibilidade e os detalhes do atendimento com o salão.'],
    'depilacao' => ['category' => 'depilacao', 'name' => 'Depilação', 'description' => 'Consulte as regiões atendidas, os métodos disponíveis e as orientações antes e depois do procedimento.'],
    'cuidados-em-casa' => ['category' => 'produtos', 'name' => 'Cuidados em casa', 'description' => 'Converse com a profissional sobre os produtos indicados para continuar seus cuidados em casa. O catálogo será adicionado em breve.'],
];

function servicoUnicoDaCategoria(array $services, string $categoria): ?string
{
    $opcoes = array_filter($services, fn ($servico) => $servico['category'] === $categoria);
    return count($opcoes) === 1 ? array_key_first($opcoes) : null;
}

function tipoAgendaServico(array $servico): string
{
    return $servico['category'] === 'unhas' ? 'Manicure' : 'Cabeleireira';
}
