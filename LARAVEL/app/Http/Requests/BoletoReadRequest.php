<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoletoReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:jpg,jpeg,png,gif,webp,pdf',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $mime = $value->getMimeType();
                    $validMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                    
                    if (!in_array($mime, $validMimes)) {
                        $fail('O arquivo deve ser uma imagem (JPG, PNG, GIF, WebP) ou PDF.');
                    }

                    // Verifica tamanho em bytes (máximo 10MB)
                    if ($value->getSize() > 10 * 1024 * 1024) {
                        $fail('O arquivo não pode ser maior que 10MB.');
                    }

                    // Valida se é realmente uma imagem ou PDF
                    try {
                        $path = $value->getRealPath();
                        if ($mime !== 'application/pdf') {
                            $imageInfo = @getimagesize($path);
                            if (!$imageInfo) {
                                $fail('O arquivo não é uma imagem válida.');
                            }
                        }
                    } catch (\Exception $e) {
                        $fail('Erro ao validar o arquivo.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Nenhum arquivo foi enviado.',
            'file.file' => 'O campo enviado não é um arquivo válido.',
            'file.max' => 'O arquivo não pode ser maior que 10MB.',
            'file.mimes' => 'Formato não suportado. Use JPG, PNG, GIF, WebP ou PDF.',
        ];
    }
}
