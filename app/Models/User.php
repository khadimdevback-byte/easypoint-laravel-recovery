<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $fillable = [
        'user_type',
        'company_id',
        'n_social',
        'name',
        'first_name',
        'username',
        'email',
        'contract_start_date',
        'contract_end_date',
        'password',
        'phone',
        'status',
        'address',
        'employee_function',
        'qualification',
        'employee_quotient',
        'identity_document_type',
        'id_number',
        'reason',
        'salary_rate',
        'avatar',
        'type_horaire',
        'emergency_contact_name',
        'emergency_contact_email',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'emergency_contact_address',
        'customer_type',
        'monday_checked',
        'start_time_monday',
        'end_time_monday',
        'one_signal_user_id',

        'tuesday_checked',
        'start_time_tuesday',
        'end_time_tuesday',

        'wednesday_checked',
        'start_time_wednesday',
        'end_time_wednesday',

        'thursday_checked',
        'start_time_thursday',
        'end_time_thursday',

        'friday_checked',
        'start_time_friday',
        'end_time_friday',

        'saturday_checked',
        'start_time_saturday',
        'end_time_saturday',

        'sunday_checked',
        'start_time_sunday',
        'end_time_sunday',
        'matricule',
        'exception_fiche_fixe',
        'salaire_fixe'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_user');
    }

    public function fonction()
{
    return $this->belongsTo(Fonction::class, 'employee_function');
}


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'equipe_id');
    }

     public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'equipe_user', 'user_id', 'equipe_id')
                    ->withTimestamps();
    }

    // Dans le modèle User

    public function attendanceRotations()
    {
        return $this->hasMany(AttendanceRotation::class, 'user_id');  // Assurez-vous que 'user_id' est la clé étrangère dans attendance_rotations
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }
    
    public function hasFeature(string $slug): bool
    {
        // 1. Le Super Admin a toujours tous les droits
        if ($this->user_type === 'super_admin') {
            return true;
        }

        // 2. Si l'utilisateur n'est pas lié à une compagnie, aucun accès
        if (!$this->company_id) {
            return false;
        }

        // 3. Requête SQL directe : Est-ce que la compagnie de l'utilisateur possède une souscription active
        // liée à un plan qui contient la fonctionnalité (feature) avec ce slug précis ?
        return \App\Models\Subscription::where('company_id', $this->company_id)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereHas('plan.features', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->exists(); // Renvoie true si trouvé, false sinon
    }

    public function activeSubscription()
    {
        // On lie l'ID de l'utilisateur à la colonne company_id de la table subscriptions
        return $this->hasOne(Subscription::class, 'company_id')
                    ->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->latestOfMany();
    }

}
