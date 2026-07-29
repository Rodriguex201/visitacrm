<?php

namespace App\Support;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class VisitaCatalogos
{
    private const DEFAULT_BADGE_CLASS = 'bg-slate-100 text-slate-700';

    private const ESTADOS = [
        'programada' => [
            'label' => 'Programada',
            'badge_class' => 'bg-blue-100 text-blue-700',
            'calendar' => [
                'backgroundColor' => '#DBEAFE',
                'borderColor' => '#93C5FD',
                'textColor' => '#1E3A8A',
            ],
        ],
        'realizada' => [
            'label' => 'Realizada',
            'badge_class' => 'bg-emerald-100 text-emerald-700',
            'calendar' => [
                'backgroundColor' => '#DCFCE7',
                'borderColor' => '#86EFAC',
                'textColor' => '#14532D',
            ],
        ],
        'cancelada' => [
            'label' => 'Cancelada',
            'badge_class' => 'bg-rose-100 text-rose-700',
            'calendar' => [
                'backgroundColor' => '#FEE2E2',
                'borderColor' => '#FCA5A5',
                'textColor' => '#7F1D1D',
            ],
        ],
    ];

    private const RESULTADOS = [
        'venta_realizada' => [
            'label' => 'Venta realizada',
            'badge_class' => 'bg-emerald-100 text-emerald-700',
        ],
        'en_seguimiento' => [
            'label' => 'En seguimiento',
            'badge_class' => 'bg-amber-100 text-amber-700',
        ],
        'sin_interes' => [
            'label' => 'Sin interés',
            'badge_class' => 'bg-rose-100 text-rose-700',
        ],
        'no_disponible' => [
            'label' => 'No disponible',
            'badge_class' => 'bg-slate-200 text-slate-700',
        ],
    ];

    private const NIVELES_INTERES = [
        'alto' => 'Alto',
        'medio' => 'Medio',
        'bajo' => 'Bajo',
        'sin_interes' => 'Sin interés',
    ];

    private const DEFAULT_ESTADO = 'programada';

    private const RESULTADOS_CON_INTERES_POSITIVO = [
        'venta_realizada',
        'en_seguimiento',
    ];

    public static function estados(): array
    {
        return self::ESTADOS;
    }

    public static function resultados(): array
    {
        return self::RESULTADOS;
    }

    public static function nivelesInteres(): array
    {
        return self::NIVELES_INTERES;
    }

    public static function defaultEstado(): string
    {
        return self::DEFAULT_ESTADO;
    }

    public static function estadoValues(): array
    {
        return array_keys(self::ESTADOS);
    }

    public static function resultadoValues(): array
    {
        return array_keys(self::RESULTADOS);
    }

    public static function nivelInteresValues(): array
    {
        return array_keys(self::NIVELES_INTERES);
    }

    public static function estadoRule(): In
    {
        return Rule::in(self::estadoValues());
    }

    public static function resultadoRule(): In
    {
        return Rule::in(self::resultadoValues());
    }

    public static function nivelInteresRule(): In
    {
        return Rule::in(self::nivelInteresValues());
    }

    public static function estadoLabel(?string $estado): ?string
    {
        return self::ESTADOS[$estado]['label'] ?? null;
    }

    public static function resultadoLabel(?string $resultado): ?string
    {
        return self::RESULTADOS[$resultado]['label'] ?? null;
    }

    public static function nivelInteresLabel(?string $nivelInteres): ?string
    {
        return self::NIVELES_INTERES[$nivelInteres] ?? null;
    }

    public static function estadoBadgeClass(?string $estado, string $default = self::DEFAULT_BADGE_CLASS): string
    {
        return self::ESTADOS[$estado]['badge_class'] ?? $default;
    }

    public static function resultadoBadgeClass(?string $resultado, string $default = self::DEFAULT_BADGE_CLASS): string
    {
        return self::RESULTADOS[$resultado]['badge_class'] ?? $default;
    }

    public static function estadoCalendarColors(?string $estado = null): array
    {
        if ($estado !== null) {
            return self::ESTADOS[$estado]['calendar'] ?? self::ESTADOS[self::DEFAULT_ESTADO]['calendar'];
        }

        return collect(self::ESTADOS)
            ->mapWithKeys(fn (array $config, string $value) => [$value => $config['calendar']])
            ->all();
    }

    public static function formOptions(string $tipo): array
    {
        $catalogo = match ($tipo) {
            'estados' => self::ESTADOS,
            'resultados' => self::RESULTADOS,
            'niveles_interes' => collect(self::NIVELES_INTERES)
                ->map(fn (string $label) => ['label' => $label])
                ->all(),
            default => [],
        };

        return collect($catalogo)
            ->map(fn (array $config, string $value) => [
                'value' => $value,
                'label' => $config['label'],
            ])
            ->values()
            ->all();
    }

    public static function frontendPayload(): array
    {
        return [
            'default_estado' => self::defaultEstado(),
            'estados' => self::ESTADOS,
            'resultados' => self::RESULTADOS,
            'niveles_interes' => self::NIVELES_INTERES,
            'resultados_con_interes_positivo' => self::RESULTADOS_CON_INTERES_POSITIVO,
            'resultado_sin_interes' => 'sin_interes',
            'resultado_no_disponible' => 'no_disponible',
            'nivel_interes_sin_interes' => 'sin_interes',
        ];
    }

    public static function resultadosConInteresPositivo(): array
    {
        return self::RESULTADOS_CON_INTERES_POSITIVO;
    }

    public static function resultadoEnSeguimiento(): string
    {
        return 'en_seguimiento';
    }

    public static function nivelInteresSinInteres(): string
    {
        return 'sin_interes';
    }

    public static function resultadoImponeNivelSinInteres(?string $resultado): bool
    {
        return $resultado === 'sin_interes';
    }

    public static function resultadoLimpiaNivelInteres(?string $resultado): bool
    {
        return $resultado === 'no_disponible';
    }

    public static function resultadoRequiereNivelDistintoDeSinInteres(?string $resultado): bool
    {
        return in_array($resultado, self::RESULTADOS_CON_INTERES_POSITIVO, true);
    }
}
