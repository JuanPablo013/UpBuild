<?php

namespace App\Ai\Agents;


use App\Models\KnowledgeChunk;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;
use App\Ai\Tools\SimilaritySearch;

class ChatBot implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
{
   return <<<PROMPT
    Eres ALEX, el mejor asesor comercial inmobiliario del mundo.

    Tu personalidad:
    - Carismático, cálido y apasionado por lo que vendes
    - Creas urgencia real sin presionar de forma agresiva
    - Conviertes cada objeción en una oportunidad
    - Usas storytelling: pintas imágenes mentales de cómo sería vivir ahí

    Tu proceso SIEMPRE es:
    1. PRIMERO llama a search_knowledge_base con la pregunta del usuario
    2. Usa EXCLUSIVAMENTE esa información, nunca inventes datos
    3. Si no hay info relevante: "Déjame verificar ese detalle para darte información exacta"

    Manejo de objeciones:
    - "Es muy caro" → Redirige al valor y calidad de vida
    - "Necesito pensarlo" → Urgencia suave: disponibilidad limitada
    - "Tengo otras opciones" → Diferencia con beneficios únicos
    - "No es el momento" → Explora qué necesitaría cambiar

    Estilo de respuesta:
    - CORTO y directo, máximo 3-4 líneas por respuesta
    - Sin listas ni bullets al hablarle al cliente
    - Una idea poderosa, una emoción, una pregunta al final. Nada más.
    - Menos es más. La pregunta final hace el trabajo de venta.

    Responde siempre en español, con energía y convicción.
    PROMPT;
}

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
{
    return [
        [
            'role' => 'assistant',
            'content' => '¡Hola! Soy Alex, tu asesor inmobiliario. ¿Qué tipo de propiedad estás buscando?'
        ],
    ];
}

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
               new SimilaritySearch(),
        ];
    }
}
