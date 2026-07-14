<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;
use App\Models\Setting;

class CopilotChat extends Component
{
    public $message = '';
    public $chatHistory = [];
    public $hasApiKey = false;

    public function mount()
    {
        $this->checkApiKey();
        
        // Initialize with default session-based chat history if any, or empty
        $this->chatHistory = session()->get('copilot_chat_history', []);
    }

    public function checkApiKey()
    {
        $this->hasApiKey = !empty(Setting::get('gemini_api_key'));
    }

    public function sendMessage()
    {
        $this->message = trim($this->message);
        if (empty($this->message)) {
            return;
        }

        if (!$this->hasApiKey) {
            $this->chatHistory[] = [
                'role' => 'assistant',
                'text' => 'Por favor, configura la clave de API de Gemini en la pestaña de Ajustes > Inteligencia Artificial para poder chatear.'
            ];
            $this->message = '';
            return;
        }

        $userQuestion = $this->message;
        
        // Push user message
        $this->chatHistory[] = [
            'role' => 'user',
            'text' => $userQuestion
        ];

        $this->message = '';
        
        // Save current history to session
        session()->put('copilot_chat_history', $this->chatHistory);
        $this->dispatch('scroll-chat-bottom');

        // Execute LLM generation (this is blocking but we show a loader spinner via Livewire loading)
        try {
            $response = GeminiService::askCopilot(
                array_slice($this->chatHistory, 0, -1), // Send previous history
                $userQuestion
            );

            // Push model response
            $this->chatHistory[] = [
                'role' => 'assistant',
                'text' => $response
            ];
        } catch (\Exception $e) {
            $this->chatHistory[] = [
                'role' => 'assistant',
                'text' => 'Lo siento, ocurrió un problema al conectar con Gemini: ' . $e->getMessage()
            ];
        }

        // Save updated history
        session()->put('copilot_chat_history', $this->chatHistory);
        $this->dispatch('scroll-chat-bottom');
    }

    public function askQuickQuestion($question)
    {
        $this->message = $question;
        $this->sendMessage();
    }

    public function clearChat()
    {
        $this->chatHistory = [];
        session()->forget('copilot_chat_history');
    }

    public function render()
    {
        return view('livewire.copilot-chat');
    }
}
