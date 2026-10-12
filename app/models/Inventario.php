<?php

namespace app\models;

use DateTimeImmutable;

class Inventario
{
    public function __construct(
        private int $id = 0,
        private int $idFuncionario = 0,
        private ?Item $item = null,
        private ?DateTimeImmutable $dataAquisicao = null,
        private bool $emUso = false
    ) {
        $this->item ??= new Item();
    }

    public static function arrayParaObjeto(array $registro): self
    {
        $data = $registro['data_aquisicao'] ?? null;
        return new self(
            (int) ($registro['id_inventario'] ?? 0),
            (int) ($registro['id_funcionario'] ?? 0),
            Item::arrayParaObjeto($registro),
            $data ? new DateTimeImmutable($data) : null,
            in_array($registro['em_uso'] ?? 0, [1, '1', true, "\x01"], true)
        );
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getIdFuncionario(): int
    {
        return $this->idFuncionario;
    }
    public function getItem(): Item
    {
        return $this->item;
    }
    public function getDataAquisicao(): ?DateTimeImmutable
    {
        return $this->dataAquisicao;
    }
    public function isEmUso(): bool
    {
        return $this->emUso;
    }
}
