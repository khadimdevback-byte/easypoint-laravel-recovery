<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\SiteUser;
use App\Models\Attendance;
use Illuminate\Console\Command;
use App\Services\OneSignalService;

class SendAttendanceNotifications extends Command
{

    protected $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        parent::__construct();
        $this->oneSignal = $oneSignal;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'app:send-attendance-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Récupérer tous les utilisateurs actifs
        $users = User::where('user_type', 'user')->where('status', 'active')->get();

        foreach ($users as $user) {
            // Récupérer les sites assignés à l'utilisateur


            $siteIds = SiteUser::where('user_id', $user->id)->pluck('site_id');
            $sites = Site::whereIn('id', $siteIds)->get();


            foreach ($sites as $site) {
                // Déterminer le jour actuel (ex: 'monday')
                $dayOfWeek = strtolower($now->format('l'));

                // Récupérer les heures de début et de fin
                $startTime = $user->{'start_time_' . $dayOfWeek} ?? $site->{'start_time_' . $dayOfWeek};
                $endTime = $user->{'end_time_' . $dayOfWeek} ?? $site->{'end_time_' . $dayOfWeek};

                if (!$startTime || !$endTime) {
                    continue; // Si aucune heure définie, passer au site suivant
                }

                // Convertir en objets Carbon avec le fuseau horaire du site
                $startTime = Carbon::parse($startTime, $site->timezone ?? 'UTC')
                    ->setDate($now->year, $now->month, $now->day);

                $endTime = Carbon::parse($endTime, $site->timezone ?? 'UTC')
                    ->setDate($now->year, $now->month, $now->day);

                // Récupérer le dernier pointage d'arrivée
                $lastCheckIn = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->where('type', 'arrivee')
                    ->whereDate('timestamp', $now->toDateString())
                    ->latest('timestamp')
                    ->first();

                // Récupérer le dernier pointage de départ
                $lastCheckOut = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->where('type', 'depart')
                    ->whereDate('timestamp', $now->toDateString())
                    ->latest('timestamp')
                    ->first();

                // 🔔 Notification pour l'arrivée
                if (!$lastCheckIn || $now->greaterThan($lastCheckIn->timestamp)) {
                    if ($now->equalTo($startTime->copy()->subMinutes(15))) {
                        $this->sendNotification($user, "Rappel de pointage", "N'oubliez pas de pointer votre arrivée !");
                    }

                    if ($now->equalTo($startTime->copy()->addMinutes(15))) {
                        $this->sendNotification($user, "Retard de pointage", "Vous êtes en retard pour votre pointage d'arrivée !");
                    }
                }

                // 🔔 Notification pour le départ
                if ($lastCheckIn && (!$lastCheckOut || $lastCheckOut->timestamp < $lastCheckIn->timestamp)) {
                    if ($now->equalTo($endTime->copy()->subMinutes(15))) {
                        $this->sendNotification($user, "Rappel de sortie", "N'oubliez pas de pointer votre départ !");
                    }

                    if ($now->equalTo($endTime->copy()->addMinutes(15))) {
                        $this->sendNotification($user, "Retard de sortie", "Vous avez oublié de pointer votre départ !");
                    }
                }
            }
        }

        $this->info('Notifications envoyées avec succès.');
    }

    private function sendNotification($user, $title, $message)
    {
        if ($user->one_signal_user_id) {
            $this->oneSignal->sendToUser($user->one_signal_user_id, $title, $message,['attendance'=>'notification',]);
        }
    }
}
