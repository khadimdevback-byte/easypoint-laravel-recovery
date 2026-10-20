<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Notifications\SendCreateCompanyEmailNotification;
use App\Notifications\UserPointedNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->first();
        $superAdmin = User::where('email', 'superadmin@example.com')->first();

        if ($company && $superAdmin) {
            $companyMessage = "Nouvelle entreprise ajoutée : {$company->compagny_name}.";
            $alreadySeeded = $superAdmin->notifications()
                ->where('type', SendCreateCompanyEmailNotification::class)
                ->get()
                ->contains(fn ($notification) => ($notification->data['message'] ?? null) === $companyMessage);

            if (!$alreadySeeded) {
                $superAdmin->notifications()->create([
                    'id' => (string) Str::uuid(),
                    'type' => SendCreateCompanyEmailNotification::class,
                    'data' => [
                        'message' => $companyMessage,
                        'company_name' => $company->compagny_name,
                        'url' => url('/companies'),
                    ],
                ]);
            }
        }

        $employee = User::where('email', 'user.demo@example.test')->first();

        if (!$employee || !$employee->company_id) {
            return;
        }

        $recipients = User::where('company_id', $employee->company_id)
            ->whereIn('user_type', ['admin', 'manager'])
            ->get();

        $message = $employee->name . " a pointé l'arrivée à 08:05.";

        foreach ($recipients as $recipient) {
            $alreadySeeded = $recipient->notifications()
                ->where('type', UserPointedNotification::class)
                ->get()
                ->contains(fn ($existing) => ($existing->data['message'] ?? null) === $message);

            if (!$alreadySeeded) {
                $recipient->notify(new UserPointedNotification($employee, Carbon::today()->setTime(8, 5)));
            }
        }
    }
}
