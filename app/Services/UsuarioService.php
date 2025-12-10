<?php
// app/Services/UsuarioService.php

namespace App\Services;

use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsuarioService
{
    protected $repository;

    public function __construct(UsuarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUsuario(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Validar email/correo único
            $emailField = isset($data['email']) ? 'email' : 'correo';
            $emailValue = $data[$emailField];
            
            $existe = $emailField === 'email' 
                ? $this->repository->findByEmail($emailValue)
                : $this->repository->findByCorreo($emailValue);
            
            if ($existe) {
                throw new \Exception('Ya existe un usuario con ese correo electrónico');
            }

            // Validar contraseña
            if (empty($data['password'])) {
                throw new \Exception('La contraseña es obligatoria');
            }

            if (strlen($data['password']) < 6) {
                throw new \Exception('La contraseña debe tener al menos 6 caracteres');
            }

            // Procesar foto si existe
            if (isset($data['foto_usuario']) && $data['foto_usuario']) {
                $data['foto_usuario_url'] = $this->procesarFoto($data['foto_usuario']);
                unset($data['foto_usuario']);
            }

            // Crear usuario
            $usuario = $this->repository->create($data);

            Log::info('Usuario creado', [
                'usuario_id' => $usuario->id_usuario ?? $usuario->id,
                'creado_por' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear usuario', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage(),
            ];
        }
    }

    public function updateUsuario(int $id, array $data): array
    {
        DB::beginTransaction();
        
        try {
            $usuario = $this->repository->findById($id);
            
            if (!$usuario) {
                throw new \Exception('Usuario no encontrado');
            }

            // Validar email/correo único (excluyendo el usuario actual)
            $emailField = isset($data['email']) ? 'email' : 'correo';
            $emailValue = $data[$emailField] ?? null;
            
            if ($emailValue) {
                $existe = $emailField === 'email' 
                    ? $this->repository->findByEmail($emailValue)
                    : $this->repository->findByCorreo($emailValue);
                
                $primaryKey = $usuario->getKeyName();
                if ($existe && $existe->$primaryKey != $id) {
                    throw new \Exception('Ya existe otro usuario con ese correo electrónico');
                }
            }

            // Validar contraseña si viene
            if (!empty($data['password']) && strlen($data['password']) < 6) {
                throw new \Exception('La contraseña debe tener al menos 6 caracteres');
            }

            // Procesar foto nueva si existe
            if (isset($data['foto_usuario']) && $data['foto_usuario']) {
                // Eliminar foto anterior si existe
                if ($usuario->foto_usuario_url) {
                    $this->eliminarFoto($usuario->foto_usuario_url);
                }
                
                $data['foto_usuario_url'] = $this->procesarFoto($data['foto_usuario']);
                unset($data['foto_usuario']);
            }

            $updated = $this->repository->update($id, $data);

            if (!$updated) {
                throw new \Exception('No se pudo actualizar el usuario');
            }

            Log::info('Usuario actualizado', [
                'usuario_id' => $id,
                'actualizado_por' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteUsuario(int $id): array
    {
        DB::beginTransaction();
        
        try {
            $usuario = $this->repository->findById($id);
            
            if (!$usuario) {
                throw new \Exception('Usuario no encontrado');
            }

            // Validar que no sea el usuario actual
            $currentUserId = auth()->user()->id_usuario ?? auth()->user()->id;
            if ($usuario->id_usuario == $currentUserId || $usuario->id == $currentUserId) {
                throw new \Exception('No puedes eliminar tu propio usuario');
            }

            // Verificar si tiene reportes asociados
            $estadisticas = $this->repository->getEstadisticasUsuario($id);
            
            if ($estadisticas['reportes_creados'] > 0 || $estadisticas['reportes_revisados'] > 0) {
                throw new \Exception('No se puede eliminar un usuario con reportes asociados. Reportes creados: ' . $estadisticas['reportes_creados'] . ', Reportes revisados: ' . $estadisticas['reportes_revisados']);
            }

            // Eliminar foto si existe
            if ($usuario->foto_usuario_url) {
                $this->eliminarFoto($usuario->foto_usuario_url);
            }

            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new \Exception('No se pudo eliminar el usuario');
            }

            Log::warning('Usuario eliminado', [
                'usuario_id' => $id,
                'usuario' => $usuario->nombre ?? $usuario->name,
                'eliminado_por' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Usuario eliminado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage(),
            ];
        }
    }

    public function verificarPassword(int $id, string $password): array
    {
        try {
            $valido = $this->repository->verificarPassword($id, $password);

            return [
                'success' => $valido,
                'message' => $valido ? 'Contraseña correcta' : 'Contraseña incorrecta',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al verificar contraseña: ' . $e->getMessage(),
            ];
        }
    }

    protected function procesarFoto($foto): string
    {
        try {
            // Generar nombre único
            $nombreArchivo = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            
            // Guardar en storage/app/public/usuarios
            $path = $foto->storeAs('public/usuarios', $nombreArchivo);
            
            // Retornar URL pública
            return Storage::url($path);
            
        } catch (\Exception $e) {
            Log::error('Error al procesar foto', ['error' => $e->getMessage()]);
            throw new \Exception('Error al subir la foto');
        }
    }

    protected function eliminarFoto(string $fotoUrl): void
    {
        try {
            // Extraer el path del storage desde la URL
            $path = str_replace('/storage/', 'public/', $fotoUrl);
            
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar foto', ['error' => $e->getMessage()]);
        }
    }
}