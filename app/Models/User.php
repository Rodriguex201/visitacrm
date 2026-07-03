<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const CODIGO_PREFIXES = [
        'freelance' => 'F-',
        'vinculado' => 'V-',
        'administracion' => 'A-',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'codigo',
        'name',
        'telefono',
        'direccion',
        'usuario_de_id',
        'banco_id',
        'cta_banco',
        'ciudad',
        'email',
        'password',
        'tipo_usuario',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }


    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    public function empresasReferidas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'responsable_user_id');
    }

    public function usuarioDe(): BelongsTo
    {
        return $this->belongsTo(self::class, 'usuario_de_id');
    }

    public function referidoPor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'usuario_de_id');
    }

    public function usuariosHijos(): HasMany
    {
        return $this->hasMany(self::class, 'usuario_de_id');
    }

    public function referidos(): HasMany
    {
        return $this->hasMany(self::class, 'usuario_de_id');
    }


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function codigoPrefixes(): array
    {
        return self::CODIGO_PREFIXES;
    }

    public static function tiposUsuarioDisponibles(): array
    {
        return array_keys(self::codigoPrefixes());
    }

    public static function codigoPrefixForTipo(string $tipoUsuario): string
    {
        return self::codigoPrefixes()[$tipoUsuario] ?? self::codigoPrefixes()['freelance'];
    }

    public static function codigoRegex(): string
    {
        $prefixes = array_map(
            static fn (string $prefix): string => preg_quote(rtrim($prefix, '-'), '/'),
            array_values(self::codigoPrefixes())
        );

        return '/^(' . implode('|', $prefixes) . ')-\d{4}$/';
    }
}
