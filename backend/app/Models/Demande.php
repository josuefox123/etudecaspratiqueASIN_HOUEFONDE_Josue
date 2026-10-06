<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Demande extends Model
{
    use HasFactory;

    public const STATUT_DEPOSEE = 'déposée';
    public const STATUT_EN_COURS = 'en cours de traitement';
    public const STATUT_VALIDEE = 'validée';
    public const STATUT_REJETEE = 'rejetée';

    public const TYPES_ACTES = [
        'acte de naissance',
        'casier judiciaire',
        'certificat de résidence'
    ];

    /**
     * Protection stricte contre le Mass Assignment.
     */
    protected $fillable = [
        'reference',
        'npi',
        'type_acte',
        'nombre_copies',
        'statut',
        'motif_rejet',
    ];

    protected $casts = [
        'nombre_copies' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Demande $demande): void {
            if (empty($demande->reference)) {
                $demande->reference = (string) Str::uuid();
            }
            if (empty($demande->statut)) {
                $demande->statut = self::STATUT_DEPOSEE;
            }
        });
    }
}
