<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ServicoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::select('id', 'titulo', 'descricao', 'categoria', 'valor_sugerido', 'imagem', 'status')
            ->with(['user:id,name', 'user.profile:id,user_id,avatar'])
            ->where('status', 'active')
            ->latest();

        if ($request->has('busca') && $request->busca !== '') {
            $busca = str_replace(['%', '_'], ['\\%', '\\_'], $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%", 'and')
                    ->orWhere('descricao', 'like', "%{$busca}%", 'and')
                    ->orWhere('categoria', 'like', "%{$busca}%", 'and');
            });
        }

        $servicos = $query->paginate(12);
        setPaginationPrefix($servicos, '/api/servicos');

        return response()->json($servicos);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        Gate::authorize('create', Service::class);

        $data = $request->validated();
        $data['titulo'] = sanitizeString($data['titulo']);
        $data['descricao'] = sanitizeString($data['descricao']);
        if (isset($data['categoria'])) {
            $data['categoria'] = sanitizeString($data['categoria']);
        }

        if ($request->hasFile('imagem')) {
            $file = $request->file('imagem');
            $error = validateImageFile($file);
            if ($error) return $error;
            $data['imagem'] = $file->store('servicos', 'public');
        } else {
            unset($data['imagem']);
        }

        $servico = $request->user()->services()->create($data);

        return response()->json($servico->load('user:id,name'), 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $servico = Service::select('id', 'user_id', 'titulo', 'descricao', 'categoria', 'valor_sugerido', 'imagem', 'status')
            ->with(['user:id,name', 'user.profile:id,user_id,avatar'])
            ->findOrFail($id);

        if ($servico->status !== 'active' && $servico->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Serviço não encontrado.'], 404);
        }

        return response()->json($servico);
    }

    public function update(UpdateServiceRequest $request, $id): JsonResponse
    {
        $servico = Service::findOrFail($id);
        Gate::authorize('update', $servico);

        $data = $request->validated();
        if (isset($data['titulo'])) {
            $data['titulo'] = sanitizeString($data['titulo']);
        }
        if (isset($data['descricao'])) {
            $data['descricao'] = sanitizeString($data['descricao']);
        }
        if (isset($data['categoria'])) {
            $data['categoria'] = sanitizeString($data['categoria']);
        }

        if ($request->hasFile('imagem')) {
            $file = $request->file('imagem');
            $error = validateImageFile($file);
            if ($error) return $error;
            if ($servico->imagem && Storage::disk('public')->exists($servico->imagem)) {
                Storage::disk('public')->delete($servico->imagem);
            }
            $data['imagem'] = $file->store('servicos', 'public');
        } else {
            unset($data['imagem']);
        }

        $servico->update($data);

        return response()->json($servico->load('user:id,name'));
    }

    public function destroy($id): JsonResponse
    {
        $servico = Service::withTrashed()->findOrFail($id);
        Gate::authorize('delete', $servico);

        $servico->forceDelete();

        return response()->json(['message' => 'Serviço removido com sucesso.']);
    }

    public function toggleStatus(Request $request, $id): JsonResponse
    {
        $servico = Service::findOrFail($id);
        Gate::authorize('update', $servico);

        $newStatus = $servico->status === 'active' ? 'inactive' : 'active';
        $servico->forceFill(['status' => $newStatus])->save();

        return response()->json($servico);
    }

    public function mine(Request $request): JsonResponse
    {
        $servicos = $request->user()->services()
            ->withTrashed()
            ->latest()
            ->paginate(12);

        setPaginationPrefix($servicos, '/api/servicos/mine');

        return response()->json($servicos);
    }
}
