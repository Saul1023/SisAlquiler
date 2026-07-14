<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeminiService
{
    /**
     * Generate structured system data context as a JSON block.
     */
    public static function generateDatabaseContext(): array
    {
        // 1. General settings
        $settings = [
            'company_name' => Setting::get('company_name', 'Alquileres El Sol'),
            'currency' => Setting::get('currency', 'Bs.'),
            'grace_days' => Setting::get('grace_days', '3'),
            'service_prices' => [
                'wifi' => Setting::get('wifi_price', '50.00'),
                'parking' => Setting::get('parking_price', '100.00'),
                'cleaning' => Setting::get('cleaning_price', '80.00'),
                'water_light' => Setting::get('water_light_price', '70.00'),
            ]
        ];

        // 2. Rooms summary
        $rooms = Room::all()->map(function ($r) {
            return [
                'cuarto' => $r->room_number,
                'piso' => $r->floor,
                'capacidad' => $r->capacity,
                'precio_base' => $r->price,
                'estado' => $r->status, // 'Disponible', 'Ocupado', 'Mantenimiento'
            ];
        })->toArray();

        // 3. Tenants summary
        $tenants = Tenant::all()->map(function ($t) {
            return [
                'id' => $t->id,
                'nombre' => $t->name,
                'ci_dni' => $t->identity_number,
                'telefono' => $t->phone,
                'email' => $t->email,
                'estado' => $t->status, // 'Activo', 'Inactivo'
            ];
        })->toArray();

        // 4. Active Contracts
        $contracts = Contract::with(['room', 'tenant'])->where('status', 'Activo')->get()->map(function ($c) {
            return [
                'inquilino' => $c->tenant->name ?? 'N/A',
                'cuarto' => $c->room->room_number ?? 'N/A',
                'fecha_inicio' => $c->start_date->format('Y-m-d'),
                'frecuencia' => $c->payment_frequency,
                'monto_mensual' => $c->total_price,
            ];
        })->toArray();

        // 5. Overdue or Pending Payments
        $pendingPayments = Payment::with(['contract.room', 'contract.tenant'])
            ->whereIn('status', ['Atrasado', 'Pendiente'])
            ->get()
            ->map(function ($p) {
                return [
                    'inquilino' => $p->contract->tenant->name ?? 'N/A',
                    'cuarto' => $p->contract->room->room_number ?? 'N/A',
                    'periodo' => $p->period_covered,
                    'monto_deuda' => $p->amount,
                    'dias_atraso' => $p->overdue_days,
                    'estado' => $p->status,
                ];
            })->toArray();

        // 6. Paid payments this year (aggregated for statistics)
        $paidPayments = Payment::where('status', 'Pagado')
            ->whereYear('payment_date', Carbon::now()->year)
            ->get();
            
        $monthlyRevenue = [];
        $grouped = $paidPayments->groupBy(function ($item) {
            return Carbon::parse($item->payment_date)->format('Y-m');
        });
        foreach ($grouped as $month => $items) {
            $monthlyRevenue[$month] = $items->sum('amount');
        }

        return [
            'fecha_actual' => Carbon::now()->format('Y-m-d H:i:s'),
            'ajustes' => $settings,
            'inventario_cuartos' => $rooms,
            'inquilinos' => $tenants,
            'contratos_activos' => $contracts,
            'deudas_pendientes' => $pendingPayments,
            'ingresos_mensuales_pagados' => $monthlyRevenue,
        ];
    }

    /**
     * Ask Gemini API using the conversation history and structured system context.
     */
    public static function askCopilot(array $chatHistory, string $userQuestion): string
    {
        $apiKey = Setting::get('gemini_api_key');

        if (!$apiKey) {
            return "No se ha configurado la clave de la API de Gemini. Por favor, ve a la sección de Ajustes > Inteligencia Artificial para configurarla.";
        }

        $context = self::generateDatabaseContext();
        $contextJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // System Instruction to guide the model's persona, constraints, and data access
        $systemInstruction = "Eres 'Copiloto AlquiRent', un asistente virtual de Inteligencia Artificial experto para el administrador de un sistema de gestión de alquiler de habitaciones.
Tu propósito es ayudar al administrador a analizar e interpretar los datos del negocio, responder preguntas de negocio, sugerir recordatorios de cobro y ofrecer estadísticas.

REGLAS DE RESPUESTA:
1. Responde de manera clara, profesional, concisa y en español.
2. Utiliza siempre formato Markdown enriquecido para estructurar la información (negritas, listas, y tablas si vas a listar montos o personas).
3. Basa tus respuestas únicamente en los datos provistos en el 'CONTEXTO DEL SISTEMA' en formato JSON a continuación. No inventes deudas o inquilinos.
4. Si el usuario te hace preguntas no relacionadas con la gestión de alquileres, recuérdale amablemente tu rol.
5. Siempre que hables de montos monetarios, utiliza la moneda configurada en el sistema (ej: Bs.).

CONTEXTO DEL SISTEMA (JSON DE LA BASE DE DATOS):
```json
{$contextJson}
```";

        // Build the contents payload for Gemini API including chat history
        $contents = [];

        // 1. Add context and instructions as the primary user payload or system instruction
        // Note: gemini-1.5-flash supports systemInstructions parameter in the query or in the body,
        // but putting it as a master prompt in the first message is highly compatible and robust.
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $systemInstruction . "\n\nHola. Entendido, soy tu Copiloto. ¿En qué te puedo ayudar hoy?"]
            ]
        ];
        
        $contents[] = [
            'role' => 'model',
            'parts' => [
                ['text' => "¡Hola! Estoy listo para ayudarte a analizar la información de AlquiRent. ¿Qué te gustaría consultar hoy?"]
            ]
        ];

        // 2. Append history
        foreach ($chatHistory as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [
                    ['text' => $msg['text']]
                ]
            ];
        }

        // 3. Append the active new question
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userQuestion]
            ]
        ];

        try {
            // Call Google Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.2, // Low temperature for precise data facts
                    'maxOutputTokens' => 1500,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No se pudo obtener una respuesta clara de la IA.';
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Error desconocido';
                return "Ocurrió un error al consultar con el servicio de IA: {$errorMessage} (Código: {$response->status()})";
            }
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return "No se pudo establecer conexión con el Copiloto IA. Detalle: " . $e->getMessage();
        }
    }
}
