<?php
// app/DAO/Interfaces/LugarDAOInterface.php

namespace App\DAO\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface LugarDAOInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findBy(string $field, $value): Collection;
    public function count(): int;
    public function exists(int $id): bool;
    
    // Métodos específicos de Lugar
    public function findByNombre(string $nombre): ?Model;
    public function getLugaresActivos(): Collection;
    public function getWithMateriales(int $id): ?Model;
    public function getWithUsuarios(int $id): ?Model;
}