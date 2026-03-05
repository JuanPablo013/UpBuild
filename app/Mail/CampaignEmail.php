<?php

namespace App\Mail;

use App\Models\AutomaticMessage;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public AutomaticMessage $message,
        public Client $client,
        public array $customData = []
    ) {
        // Configurar cola específica
        $this->onQueue('emails');

        // Configurar prioridad si es urgente
        if ($client->priority === 'urgent') {
            $this->onQueue('high');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    config('mail.from.address'),
                    config('mail.from.name')
                ),
            ],
            subject: $this->message->subject ?? 'Nueva campaña',
            tags: [
                'campaign-' . $this->message->campaign_id,
                'team-' . $this->message->campaign->team_id,
            ],
            metadata: [
                'campaign_id' => $this->message->campaign_id,
                'message_id' => $this->message->id,
                'client_id' => $this->client->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaign',
            with: [
                'client' => $this->client,
                'message' => $this->message,
                'campaign' => $this->message->campaign,
                'content' => $this->parseContent(),
                'customData' => $this->customData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Parse content with variables
     */
    private function parseContent(): string
    {
        $content = $this->message->content;

        // Reemplazar variables dinámicas
        $replacements = [
            '{name}' => $this->client->name,
            '{email}' => $this->client->email,
            '{telephone}' => $this->client->telephone ?? 'N/A',
            '{city}' => $this->client->city ?? 'N/A',
            '{campaign}' => $this->message->campaign->name,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $content
        );
    }
}
