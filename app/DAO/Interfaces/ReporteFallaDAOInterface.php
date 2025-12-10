<?php
// app/DAO/Interfaces/ReporteFallaDAOInterface.php

namespace App\DAO\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ReporteFallaDAOInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
    
    // Métodos específicos de ReporteFalla
    public function getByLugar(int $idLugar): Collection;
    public function getByVehiculo(string $eco): Collection;
    public function getByUsuarioReporta(int $idUsuario): Collection;
    public function getByUsuarioRevisa(int $idUsuario): Collection;
    public function getPendientes(int $idLugar): Collection;
    public function getAprobados(int $idLugar): Collection;
    public function getRechazados(int $idLugar): Collection;
    public function aprobar(int $id, array $data): bool;
    public function rechazar(int $id, array $data): bool;
}