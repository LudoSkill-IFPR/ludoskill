<?php

require __DIR__ . '/../app/core/Autoload.php';

use app\helpers\Validador;

if (!Validador::cpfValido('529.982.247-25')) throw new RuntimeException('Um CPF válido foi rejeitado.');
if (Validador::cpfValido('111.111.111-11')) throw new RuntimeException('Um CPF repetido foi aceito.');
if (Validador::cpfValido('529.982.247-24')) throw new RuntimeException('Um CPF com dígito incorreto foi aceito.');

if (!Validador::telefoneValido('(45) 99841-0144')) throw new RuntimeException('Um celular válido foi rejeitado.');
if (!Validador::telefoneValido('(45) 3303-1234')) throw new RuntimeException('Um telefone fixo válido foi rejeitado.');
if (Validador::telefoneValido('(00) 99841-0144')) throw new RuntimeException('Um DDD inválido foi aceito.');

if (!Validador::dataNascimentoValida('2000-02-29')) throw new RuntimeException('Uma data válida foi rejeitada.');
if (Validador::dataNascimentoValida('2001-02-29')) throw new RuntimeException('Uma data inexistente foi aceita.');
if (Validador::dataNascimentoValida((new DateTimeImmutable('tomorrow'))->format('Y-m-d'))) throw new RuntimeException('Uma data futura foi aceita.');

echo "Validações cadastrais verificadas.\n";
