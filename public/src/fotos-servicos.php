<?php
// Reutiliza os arquivos, descrições e dimensões já definidos na galeria.
require_once __DIR__ . '/galeria.php';

function buscarFotoGaleria(string $arquivo): ?array
{
    global $fotosGaleria;
    foreach ($fotosGaleria as $foto) {
        if (basename($foto['arquivo']) === $arquivo) return $foto;
    }
    return null; // Sem correspondência: o modelo mantém a ilustração.
}

$fotoMassagem = [
    'arquivo' => '/images/ilustrativas/massagem.webp',
    'alt' => 'Mãos massageando os ombros sobre uma maca',
    'largura' => 1000,
    'altura' => 563,
    'tipo' => 'Ilustrativa',
];

$fotoDepilacao = [
    'arquivo' => '/images/ilustrativas/depilacao.webp',
    'alt' => 'Pote de cera, espátula e toalha para depilação',
    'largura' => 1000,
    'altura' => 667,
    'tipo' => 'Ilustrativa',
];

// Categorias da página inicial e da página geral de serviços.
$fotosCategorias = [
    'massagem' => $fotoMassagem,
    'depilacao' => $fotoDepilacao,
    'produtos' => buscarFotoGaleria('2025-07-03-oleo-elixir-ilumina.jpg'),
    'unhas' => buscarFotoGaleria('2025-09-05-manicure-cinza-prata-duas-maos.jpg'),
    'cabelo' => buscarFotoGaleria('2024-07-05-mechas-morena-iluminada.jpg'),
    'sobrancelha' => buscarFotoGaleria('2025-12-09-design-em-atendimento.jpg'),
];

// Fotos de serviços, incluindo referências ilustrativas identificadas.
$fotosServicos = [
    'pedicure' => [
        'arquivo' => '/images/ilustrativas/pedicure.webp',
        'alt' => 'Aplicação de esmalte rosa nas unhas dos pés',
        'largura' => 1000,
        'altura' => 667,
        'tipo' => 'Ilustrativa',
    ],
    'massagem-relaxante' => $fotoMassagem,
    'depilacao' => $fotoDepilacao,
    'cuidados-em-casa' => buscarFotoGaleria('2025-07-03-oleo-elixir-ilumina.jpg'),
    'escova' => buscarFotoGaleria('2024-08-21-corte-escova-modelada-ruivo.jpg'),
    'manicure' => buscarFotoGaleria('2021-04-01-manicure-branca-em-atendimento.jpg'),
    'corte' => buscarFotoGaleria('2024-08-30-corte-chanel-resultado.jpg'),
    'luzes' => buscarFotoGaleria('2024-07-05-mechas-morena-iluminada.jpg'),
    'coloracao' => buscarFotoGaleria('2024-07-01-corte-coloracao-curto.jpg'),
    'tratamento-capilar' => buscarFotoGaleria('2026-10-21-corte-luzes-hidratacao.jpg'),
    'design' => buscarFotoGaleria('2025-12-09-design-em-atendimento.jpg'),
];
