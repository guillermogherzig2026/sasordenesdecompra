<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'company_id' => ['required', 'exists:companies,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'responsible_user_id' => ['nullable', 'exists:users,id'],
            'project_key' => ['required', 'string', 'max:50', Rule::unique('projects', 'project_key')->ignore($projectId)],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'modality' => ['required', Rule::in(['Precio alzado', 'Administracion'])],
            'status' => ['required', Rule::in(['Por iniciar', 'En Proceso', 'Terminada'])],
            'start_date' => ['nullable', 'date'],
            'estimated_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'contracted_value' => ['required', 'numeric', 'min:0'],
            'estimated_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'retention_amount' => ['nullable', 'numeric', 'min:0'],
            'physical_progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'financial_progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photo_path' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_key' => 'clave de obra',
            'company_id' => 'empresa',
            'client_id' => 'cliente',
            'responsible_user_id' => 'responsable',
            'modality' => 'modalidad',
            'contracted_value' => 'valor contratado',
            'estimated_end_date' => 'fecha estimada de terminacion',
        ];
    }
}
