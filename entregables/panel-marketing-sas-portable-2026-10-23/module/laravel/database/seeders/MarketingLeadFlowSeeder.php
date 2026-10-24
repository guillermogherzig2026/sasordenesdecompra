<?php

namespace Database\Seeders;

use App\Models\Marketing\AutomationRule;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\Marketing\Message;
use App\Models\Marketing\OfficeHour;
use App\Models\Marketing\Tag;
use App\Models\Marketing\User;
use App\Models\Marketing\WhatsAppNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketingLeadFlowSeeder extends Seeder
{
    public function run(): void
    {
        if (Company::query()->exists()) {
            return;
        }

        $companies = collect([
            ['Zen Dragon Labs', 'zendragonlabs.com', 'Tecnología', 'ZD', '#063f2c'],
            ['Luzi Café', 'luzicafe.mx', 'Alimentos', 'LC', '#caa47a'],
            ['Prodifem', 'prodifem.com', 'Salud', 'P', '#5b35c9'],
            ['Zen Dragon Promos', 'zendragonpromos.com', 'Marketing', 'ZG', '#043724'],
            ['Zen Dragon Lanzamiento', 'lanzamiento.zendragon.com', 'Educación', 'ZL', '#16a34a'],
            ['Otros', null, 'Servicios', '...', '#d1d5db'],
        ])->map(function (array $item): Company {
            $active = $item[0] !== 'Otros';

            return Company::create([
                'name' => $item[0],
                'slug' => Str::slug($item[0]).'-'.Str::lower(Str::random(4)),
                'domain' => $item[1],
                'industry' => $item[2],
                'logo_initials' => $item[3],
                'brand_color' => $item[4],
                'active' => $active,
                'status' => $active ? 'active' : 'inactive',
                'api_token' => Str::random(64),
            ]);
        });

        $zen = $companies->first();

        $users = collect([
            ['María Fernanda López', 'maria.lopez@zendragon.com', 'administrador', $zen->id, '+52 55 1234 5678'],
            ['Carlos Alberto Ruiz', 'carlos.ruiz@zendragon.com', 'supervisor', $zen->id, '+52 55 9876 5432'],
            ['Daniela Moreno', 'daniela.moreno@zendragon.com', 'asesor', $zen->id, '+52 55 2345 6789'],
            ['Juan Pablo Torres', 'juan.torres@zendragon.com', 'asesor', $zen->id, '+52 55 3456 7890'],
            ['Valentina Castro', 'valentina.castro@luzicafe.com', 'asesor', $companies[1]->id, '+52 55 4567 8901'],
            ['Diego Herrera', 'diego.herrera@prodifem.com', 'supervisor', $companies[2]->id, '+52 55 5678 9012'],
            ['Camila Rodríguez', 'camila.rodriguez@zendragon.com', 'asesor', $zen->id, '+52 55 6789 0123'],
            ['Santiago Vargas', 'santiago.vargas@zendragon.com', 'asesor', $zen->id, '+52 55 7890 1234'],
        ])->map(fn (array $item): User => User::create([
            'name' => $item[0],
            'email' => $item[1],
            'password' => Hash::make('password'),
            'role' => $item[2],
            'company_id' => $item[3],
            'phone' => $item[4],
            'active' => true,
            'last_login_at' => now()->subMinutes(rand(20, 600)),
        ]));

        $campaigns = collect([
            ['Promos Mayo', 'active', 'Promoción mensual', 15000, 12450, $users[0]->id, ['Instagram', 'Facebook']],
            ['Lanzamiento', 'active', 'Nuevo producto', 12000, 8240, $users[3]->id, ['Facebook']],
            ['Retargeting', 'active', 'Audiencias calientes', 8000, 4760, $users[1]->id, ['Meta Ads', 'Instagram']],
            ['Awareness', 'active', 'Alcance e interés', 6500, 3230, $users[2]->id, ['TikTok', 'Instagram']],
            ['Post Venta', 'paused', 'Clientes actuales', 5000, 1480, $users[4]->id, ['WhatsApp']],
            ['Fidelización', 'active', 'Lealtad y recompra', 4500, 2120, $users[0]->id, ['Facebook', 'WhatsApp']],
            ['Campaña Q1', 'ended', 'Enero - Marzo', 22000, 19800, $users[3]->id, ['Meta Ads']],
        ])->map(fn (array $item): Campaign => Campaign::create([
            'company_id' => $zen->id,
            'responsible_user_id' => $item[5],
            'name' => $item[0],
            'status' => $item[1],
            'starts_at' => now()->subMonths(2)->toDateString(),
            'ends_at' => now()->addWeeks(3)->toDateString(),
            'budget' => $item[3],
            'spent' => $item[4],
            'product' => $item[2],
            'source' => implode(', ', $item[6]),
            'social_networks' => $item[6],
            'description' => 'Campaña demo creada para probar el flujo comercial.',
        ]));

        foreach ($users as $user) {
            if ($user->company_id === $zen->id) {
                $user->campaigns()->sync($campaigns->pluck('id')->take(rand(2, 5))->all());
            }
        }

        foreach ($companies as $company) {
            for ($i = 1; $i <= ($company->id === $zen->id ? 4 : 2); $i++) {
                WhatsAppNumber::create([
                    'company_id' => $company->id,
                    'display_name' => "Ventas {$i}",
                    'phone_number' => '+52 55 '.rand(1000, 9999).' '.rand(1000, 9999),
                    'provider_phone_id' => 'phone_'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'status' => $company->active ? 'active' : 'inactive',
                ]);
            }

            foreach (['Interesado', 'Promoción', 'Cliente VIP', 'WhatsApp', 'Urgente'] as $tag) {
                Tag::create(['company_id' => $company->id, 'name' => $tag, 'color' => '#16a34a']);
            }

            foreach (range(1, 7) as $day) {
                OfficeHour::create([
                    'company_id' => $company->id,
                    'day_of_week' => $day,
                    'opens_at' => '09:00',
                    'closes_at' => '18:00',
                    'enabled' => $day <= 5,
                ]);
            }
        }

        AutomationRule::create([
            'company_id' => $zen->id,
            'campaign_id' => $campaigns[0]->id,
            'name' => 'Respuesta inicial WhatsApp',
            'trigger' => 'mensaje_recibido',
            'action' => 'enviar_respuesta',
            'payload' => ['message' => 'Gracias por escribirnos. Un asesor te atenderá en breve.'],
            'active' => true,
        ]);

        foreach ($companies->filter(fn (Company $company): bool => $company->id !== $zen->id && $company->active) as $company) {
            $companyUser = User::create([
                'name' => 'Responsable '.$company->logo_initials,
                'email' => strtolower($company->logo_initials).'@leadflow.test',
                'password' => Hash::make('password'),
                'role' => 'administrador',
                'company_id' => $company->id,
                'phone' => '+52 55 '.rand(1000, 9999).' '.rand(1000, 9999),
                'active' => true,
                'last_login_at' => now()->subHours(rand(2, 24)),
            ]);

            $companyCampaign = Campaign::create([
                'company_id' => $company->id,
                'responsible_user_id' => $companyUser->id,
                'name' => 'Campaña principal',
                'status' => 'active',
                'starts_at' => now()->subMonth()->toDateString(),
                'ends_at' => now()->addMonth()->toDateString(),
                'budget' => 7000,
                'spent' => 3400,
                'product' => 'WhatsApp',
                'source' => 'Instagram, Facebook',
                'social_networks' => ['Instagram', 'Facebook'],
                'description' => 'Campaña demo para validar datos multempresa.',
            ]);

            $companyUser->campaigns()->sync([$companyCampaign->id]);

            foreach (range(1, 4) as $leadIndex) {
                $lead = Lead::create([
                    'company_id' => $company->id,
                    'campaign_id' => $companyCampaign->id,
                    'advisor_id' => $companyUser->id,
                    'name' => "Lead {$leadIndex} {$company->logo_initials}",
                    'phone' => '+52 55 '.rand(1000, 9999).' '.rand(1000, 9999),
                    'email' => "lead{$leadIndex}.{$company->slug}@example.com",
                    'product' => 'WhatsApp',
                    'status' => ['nuevo', 'interesado', 'calificado', 'seguimiento'][$leadIndex - 1],
                    'estimated_value' => 500 + ($leadIndex * 250),
                    'last_contact_at' => now()->subHours($leadIndex),
                    'next_follow_up_at' => now()->addDays($leadIndex),
                    'source' => 'WhatsApp',
                    'tags' => ['WhatsApp'],
                ]);

                $conversation = Conversation::create([
                    'company_id' => $company->id,
                    'campaign_id' => $companyCampaign->id,
                    'lead_id' => $lead->id,
                    'assigned_user_id' => $companyUser->id,
                    'channel' => 'WhatsApp',
                    'status' => 'open',
                    'unread_count' => $leadIndex % 2,
                    'last_message_at' => $lead->last_contact_at,
                ]);

                $this->seedMessages($conversation, $lead, $companyUser);
            }
        }

        $leadNames = [
            ['María Fernanda López', '55 1234 5678', 'calificado', 1250],
            ['Carlos Alberto Ruiz', '55 9876 5432', 'seguimiento', 980],
            ['Daniela Moreno', '55 2345 6789', 'nuevo', 650],
            ['Juan Pablo Torres', '55 3456 7890', 'calificado', 2100],
            ['Valentina Castro', '55 4567 8901', 'seguimiento', 870],
            ['Ricardo Méndez', '55 5678 9012', 'nuevo', 720],
            ['Camila Rodríguez', '55 6789 0123', 'calificado', 1450],
            ['José Luis Herrera', '55 7890 1234', 'descartado', 300],
            ['Ana Sofía Ramírez', '55 8901 2345', 'seguimiento', 990],
            ['Diego Navarro', '55 9012 3456', 'nuevo', 560],
            ['Sofía Martínez', '55 0123 4567', 'interesado', 1350],
            ['Andrea Pineda', '55 1111 2222', 'convertido', 3200],
            ['Luis Romero', '55 3333 4444', 'convertido', 1850],
            ['Fernanda Vega', '55 5555 6666', 'interesado', 1120],
        ];

        foreach ($leadNames as $index => $leadData) {
            $campaign = $campaigns[$index % $campaigns->count()];
            $advisor = $users->where('company_id', $zen->id)->values()[$index % $users->where('company_id', $zen->id)->count()];

            $lead = Lead::create([
                'company_id' => $zen->id,
                'campaign_id' => $campaign->id,
                'advisor_id' => $advisor->id,
                'name' => $leadData[0],
                'phone' => $leadData[1],
                'email' => 'lead'.($index + 1).'@example.com',
                'product' => $campaign->product,
                'status' => $leadData[2],
                'estimated_value' => $leadData[3],
                'last_contact_at' => now()->subMinutes(15 + ($index * 35)),
                'next_follow_up_at' => now()->addDays(rand(1, 5)),
                'source' => 'WhatsApp',
                'position' => 'Gerente de Marketing',
                'tags' => ['Interesado', 'WhatsApp'],
                'notes' => 'Lead de prueba proveniente de campaña en redes sociales.',
            ]);

            $conversation = Conversation::create([
                'company_id' => $lead->company_id,
                'campaign_id' => $lead->campaign_id,
                'lead_id' => $lead->id,
                'assigned_user_id' => $lead->advisor_id,
                'channel' => 'WhatsApp',
                'status' => 'open',
                'unread_count' => $index % 3,
                'last_message_at' => $lead->last_contact_at,
            ]);

            $this->seedMessages($conversation, $lead, $advisor);
        }
    }

    private function seedMessages(Conversation $conversation, Lead $lead, User $advisor): void
    {
        $messages = [
            ['lead', 'in', 'text', '¡Hola! Quiero saber más sobre la promoción de este mes.', -18],
            ['user', 'out', 'text', "Hola {$lead->name}. Claro, con gusto te comparto toda la información.", -14],
            ['lead', 'in', 'text', 'Perfecto, ¿aplica para todos los servicios?', -10],
            ['user', 'out', 'text', 'Sí, aplica para los servicios seleccionados. Te envío los detalles.', -8],
            ['user', 'out', 'file', 'Promos_Mayo_2024.pdf', -7],
            ['lead', 'in', 'text', 'Gracias, lo revisaré y te escribo si tengo dudas.', -4],
            ['user', 'out', 'text', 'Perfecto, quedo atento.', -2],
        ];

        foreach ($messages as [$sender, $direction, $type, $body, $minutes]) {
            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender === 'user' ? $advisor->id : null,
                'sender_type' => $sender,
                'direction' => $direction,
                'message_type' => $type,
                'body' => $type === 'file' ? 'Archivo con información comercial.' : $body,
                'file_name' => $type === 'file' ? $body : null,
                'file_path' => $type === 'file' ? 'conversation-files/demo.pdf' : null,
                'sent_at' => $conversation->last_message_at->copy()->addMinutes($minutes + 20),
            ]);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $advisor->id,
            'sender_type' => 'system',
            'direction' => 'internal',
            'message_type' => 'note',
            'body' => 'Interesada en promoción por volumen.',
            'sent_at' => $conversation->last_message_at->copy()->addMinute(),
        ]);
    }
}
