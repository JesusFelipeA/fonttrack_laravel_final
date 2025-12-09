<?php
// app/DAO/Interfaces/MaterialDAOInterface.php

namespace App\DAO\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface MaterialDAOInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findBy(string $field, $value): Collection;
    public function getWithRelations(array $relations): Collection;
    public function count(): int;
    public function exists(int $id): bool;
    
    // Métodos específicos de Material
    public function getByLugar(int $idLugar): Collection;
    public function findByClave(string $clave, ?int $idLugar = null): ?Model;
    public function updateExistencia(int $id, int $cantidad): bool;
}