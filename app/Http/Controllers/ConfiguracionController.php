<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuracion\StoreCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\StoreSectorRequest;
use App\Http\Requests\Configuracion\UpdateCatalogoOpcionRequest;
use App\Http\Requests\Configuracion\UpdateLogoSidebarRequest;
use App\Http\Requests\Configuracion\UpdateSectorRequest;
use App\Models\Banco;
use App\Models\CatalogoOpcion;
use App\Models\ConfiguracionSistema;
use App\Models\EstadoReferidoColor;
use App\Models\HerramientaDisponible;
use App\Models\HerramientaOfrecer;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ConfiguracionController extends Controller
{
    private const CATEGORIAS = [
        'estado-actual' => 'Estado Actual',
        'aplicativos' => 'Aplicativos',
        'procesos-electronicos' => 'Procesos Electrónicos',
        'equipos' => 'Equipos',
        'como-llego' => 'Como Llego',
        'cotizaciones' => 'Cotizaciones',
    ];


    private const ESTADOS_REFERIDO = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    private const ESTADOS_REFERIDO_DEFAULT_COLORS = [
        'pendiente' => ['bg_color' => '#FEF3C7', 'text_color' => '#92400E'],
        'aprobado' => ['bg_color' => '#D1FAE5', 'text_color' => '#065F46'],
        'rechazado' => ['bg_color' => '#FEE2E2', 'text_color' => '#991B1B'],
    ];

    public function index(): View
    {
        $sectores = $this->sectoresActivos();

        $catalogo = collect(self::CATEGORIAS)
            ->mapWithKeys(fn (string $categoria, string $slug) => [
                $slug => $this->catalogoActivoPorCategoria($categoria),
            ]);

        return view('configuracion.index', [
            'sectores' => $sectores,
            'categorias' => self::CATEGORIAS,
            'catalogoPorCategoria' => $catalogo,
            'bancos' => $this->bancosListado(),
            'estadosReferidoColores' => $this->estadoReferidoColores(),
            'estadosReferidoLabels' => self::ESTADOS_REFERIDO,

            'claveAdmin' => ConfiguracionSistema::valor('clave_eliminar_empresa', 'Admin2026'),
            'clavesSistema' => $this->clavesSistema(),
            'logoSidebarActual' => ConfiguracionSistema::valor('logo_sidebar'),
            'esAdministracion' => (auth()->user()?->tipo_usuario ?? null) === 'administracion',
            'validarClaveUrl' => route('configuracion.claves.validate'),
            'actualizarClaveUrlTemplate' => route('configuracion.claves.update', ['configuracion' => '__ID__']),
            'herramientas' => $this->herramientasListado(),
            'ofrecerItems' => $this->ofrecerListado(),
        ]);
    }

    public function sectores(): JsonResponse
    {
        return response()->json(['data' => $this->sectoresActivos()]);
    }

    public function storeSector(StoreSectorRequest $request): JsonResponse
    {
        $sector = Sector::query()->create([
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Sector creado correctamente.',
            'data' => $sector,
        ], 201);
    }

    public function updateSector(UpdateSectorRequest $request, Sector $sector): JsonResponse
    {
        $sector->update([
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,
        ]);

        return response()->json([
            'message' => 'Sector actualizado correctamente.',
            'data' => $sector,
        ]);
    }

    public function destroySector(Sector $sector): JsonResponse
    {
        $sector->update(['activo' => 0]);

        return response()->json([
            'message' => 'Sector desactivado correctamente.',
        ]);
    }

    public function catalogo(string $categoria): JsonResponse
    {
        $categoriaNombre = $this->resolveCategoria($categoria);

        return response()->json([
            'data' => $this->catalogoActivoPorCategoria($categoriaNombre),
        ]);
    }

    public function storeCatalogo(StoreCatalogoOpcionRequest $request): JsonResponse
    {
        $categoriaNombre = $this->resolveCategoria($request->validated('categoria'));

        $opcion = CatalogoOpcion::query()->create([
            'categoria' => $categoriaNombre,
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,

            'valor' => $request->validated('valor'),
            'valor_vinculado' => $request->validated('valor_vinculado'),
            'valor_freelance' => $request->validated('valor_freelance'),
            'activo' => 1,
        ]);

        return response()->json([
            'message' => 'Opción creada correctamente.',
            'data' => $opcion,
        ], 201);
    }

    public function updateCatalogo(UpdateCatalogoOpcionRequest $request, CatalogoOpcion $catalogoOpcion): JsonResponse
    {
        $categoriaSlug = $request->validated('categoria');
        $categoriaNombre = $this->resolveCategoria($categoriaSlug);

        $catalogoOpcion->update([
            'categoria' => $categoriaNombre,
            'nombre' => $request->validated('nombre'),
            'orden' => $request->validated('orden') ?? 0,

            'valor' => $request->validated('valor'),
            'valor_vinculado' => $request->validated('valor_vinculado'),
            'valor_freelance' => $request->validated('valor_freelance'),
        ]);

        return response()->json([
            'message' => 'Opción actualizada correctamente.',
            'data' => $catalogoOpcion,
        ]);
    }

    public function destroyCatalogo(CatalogoOpcion $catalogoOpcion): JsonResponse
    {
        $catalogoOpcion->update(['activo' => 0]);

        return response()->json([
            'message' => 'Opción desactivada correctamente.',
        ]);
    }


    public function validarClaveAdmin(Request $request): JsonResponse
    {
        $claveIngresada = (string) $request->input('clave', '');
        $claveSistema = (string) ConfiguracionSistema::valor('clave_eliminar_empresa', 'Admin2026');

        if (! hash_equals($claveSistema, $claveIngresada)) {
            return response()->json([
                'message' => 'Clave incorrecta',
            ], 422);
        }

        return response()->json([
            'message' => 'Clave válida.',
        ]);
    }

    public function updateClaveSistema(Request $request, ConfiguracionSistema $configuracion): JsonResponse
    {
        $validated = $request->validate([
            'valor' => ['required', 'string'],
        ]);

        $configuracion->update([
            'valor' => $validated['valor'],
        ]);

        return response()->json([
            'message' => 'Clave actualizada correctamente.',
            'data' => [
                'id' => $configuracion->id,
                'clave' => $configuracion->clave,
                'nombre' => $this->humanizarClave($configuracion->clave),
                'valor' => $configuracion->valor,
                'descripcion' => $configuracion->descripcion,
            ],
        ]);
    }




    public function herramientas(): JsonResponse
    {
        return response()->json([
            'data' => $this->herramientasListado(),
        ]);
    }

    public function storeHerramienta(Request $request): JsonResponse
    {
        $validated = $this->validarHerramienta($request);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->procesarImagenHerramienta($request->file('imagen'));
        }

        $herramienta = HerramientaDisponible::query()->create($validated + [
            'orden' => $validated['orden'] ?? 0,
            'activo' => $validated['activo'] ?? true,
            'abrir_en_nueva_pestana' => $validated['abrir_en_nueva_pestana'] ?? true,
        ]);

        return response()->json([
            'message' => 'Herramienta creada correctamente.',
            'data' => $herramienta,
        ], 201);
    }

    public function updateHerramienta(Request $request, HerramientaDisponible $herramientaDisponible): JsonResponse
    {
        $validated = $this->validarHerramienta($request);
        $imagenAnterior = $herramientaDisponible->imagen;

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->procesarImagenHerramienta($request->file('imagen'));

            if ($imagenAnterior) {
                Storage::disk('public')->delete($imagenAnterior);
            }
        }

        $herramientaDisponible->update($validated);

        return response()->json([
            'message' => 'Herramienta actualizada correctamente.',
            'data' => $herramientaDisponible->fresh(),
        ]);
    }

    public function activarHerramienta(HerramientaDisponible $herramientaDisponible): JsonResponse
    {
        $herramientaDisponible->update(['activo' => true]);

        return response()->json([
            'message' => 'Herramienta activada correctamente.',
        ]);
    }

    public function desactivarHerramienta(HerramientaDisponible $herramientaDisponible): JsonResponse
    {
        $herramientaDisponible->update(['activo' => false]);

        return response()->json([
            'message' => 'Herramienta desactivada correctamente.',
        ]);
    }

    public function destroyHerramienta(HerramientaDisponible $herramientaDisponible): JsonResponse
    {
        if ($herramientaDisponible->imagen) {
            Storage::disk('public')->delete($herramientaDisponible->imagen);
        }

        $herramientaDisponible->delete();

        return response()->json([
            'message' => 'Herramienta eliminada correctamente.',
        ]);
    }

    public function updateLogoSidebar(UpdateLogoSidebarRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $archivo = $validated['logo'];

        try {
            $rutaRelativa = $this->guardarLogoSidebar($archivo);

            return redirect()
                ->route('configuracion.index', ['tab' => 'logo'])
                ->with('success', 'Logo actualizado correctamente.');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('configuracion.index', ['tab' => 'logo'])
                ->withErrors([
                    'logo' => 'No se pudo guardar el nuevo logo. Verifica permisos de la carpeta public/imagenes/logo e inténtalo nuevamente.',
                ], 'updateLogoSidebar');
        }
    }

    public function updateEstadoReferidoColor(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validateWithBag('updateEstadoReferidoColor', [
            'estado' => ['required', 'in:pendiente,aprobado,rechazado'],
            'bg_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'text_color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        EstadoReferidoColor::query()->updateOrCreate(
            ['estado' => $validated['estado']],
            [
                'bg_color' => strtoupper($validated['bg_color']),
                'text_color' => strtoupper($validated['text_color']),
                'activo' => 1,
            ]
        );

        return redirect()
            ->route('configuracion.index')
            ->with('success', 'Colores del estado del referido actualizados correctamente.');
    }

    public function ofrecer(): JsonResponse
    {
        return response()->json([
            'data' => $this->ofrecerListado(),
        ]);
    }

    public function storeOfrecer(Request $request): JsonResponse
    {
        $validated = $this->validarOfrecer($request);
        $validated['imagen'] = $this->normalizarRutaPublica(
            $this->procesarImagenOfrecer($request->file('imagen'))
        );

        $item = HerramientaOfrecer::query()->create($validated + [
            'orden' => $validated['orden'] ?? 0,
            'activo' => $validated['activo'] ?? true,
        ]);

        return response()->json([
            'message' => 'Elemento creado correctamente.',
            'data' => $item,
        ], 201);
    }

    public function updateOfrecer(Request $request, HerramientaOfrecer $herramientaOfrecer): JsonResponse
    {
        $validated = $this->validarOfrecer($request, true);
        $imagenAnterior = $herramientaOfrecer->imagen;

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->normalizarRutaPublica(
                $this->procesarImagenOfrecer($request->file('imagen'))
            );

            if ($imagenAnterior) {
                Storage::disk('public')->delete($this->normalizarRutaPublica($imagenAnterior));
            }
        }

        $herramientaOfrecer->update($validated);

        return response()->json([
            'message' => 'Elemento actualizado correctamente.',
            'data' => $herramientaOfrecer->fresh(),
        ]);
    }

    public function activarOfrecer(HerramientaOfrecer $herramientaOfrecer): JsonResponse
    {
        $herramientaOfrecer->update(['activo' => true]);

        return response()->json([
            'message' => 'Elemento activado correctamente.',
        ]);
    }

    public function desactivarOfrecer(HerramientaOfrecer $herramientaOfrecer): JsonResponse
    {
        $herramientaOfrecer->update(['activo' => false]);

        return response()->json([
            'message' => 'Elemento desactivado correctamente.',
        ]);
    }

    public function destroyOfrecer(HerramientaOfrecer $herramientaOfrecer): JsonResponse
    {
        if ($herramientaOfrecer->imagen) {
            Storage::disk('public')->delete($this->normalizarRutaPublica($herramientaOfrecer->imagen));
        }

        $herramientaOfrecer->delete();

        return response()->json([
            'message' => 'Elemento eliminado correctamente.',
        ]);
    }


    private function herramientasListado()
    {
        return HerramientaDisponible::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->get();
    }

    private function ofrecerListado()
    {
        return HerramientaOfrecer::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->get();
    }

    private function validarHerramienta(Request $request): array
    {

        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'icono' => ['nullable', 'string', 'max:255'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'color_fondo' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'color_texto' => ['nullable', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
            'abrir_en_nueva_pestana' => ['nullable', 'boolean'],

        ]);
    }

    private function procesarImagenHerramienta(UploadedFile $imagen): string
    {
        $origen = @imagecreatefromstring($imagen->get());

        if (! $origen) {
            throw new RuntimeException('No se pudo procesar la imagen cargada.');
        }

        $anchoOrigen = imagesx($origen);
        $altoOrigen = imagesy($origen);
        $lado = min($anchoOrigen, $altoOrigen);
        $origenX = (int) floor(($anchoOrigen - $lado) / 2);
        $origenY = (int) floor(($altoOrigen - $lado) / 2);

        $destino = imagecreatetruecolor(56, 56);
        imagealphablending($destino, false);
        imagesavealpha($destino, true);
        $fondoTransparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
        imagefill($destino, 0, 0, $fondoTransparente);

        imagecopyresampled(
            $destino,
            $origen,
            0,
            0,
            $origenX,
            $origenY,
            56,
            56,
            $lado,
            $lado
        );

        $mime = strtolower((string) $imagen->getMimeType());
        $extension = 'png';

        ob_start();

        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            $extension = 'jpg';
            imagejpeg($destino, null, 90);
        } elseif (str_contains($mime, 'webp') && function_exists('imagewebp')) {
            $extension = 'webp';
            imagewebp($destino, null, 90);
        } else {
            imagepng($destino, null, 7);
        }

        $contenido = ob_get_clean();

        imagedestroy($origen);
        imagedestroy($destino);

        $nombreArchivo = now()->format('YmdHis') . '-' . str()->random(10) . '.' . $extension;
        $rutaRelativa = 'herramientas/' . $nombreArchivo;

        Storage::disk('public')->put($rutaRelativa, $contenido);

        return $rutaRelativa;
    }

    private function guardarLogoSidebar(UploadedFile $archivo): string
    {
        $directorioRelativo = 'imagenes/logo';
        $directorioAbsoluto = public_path($directorioRelativo);

        if (! File::exists($directorioAbsoluto)) {
            File::ensureDirectoryExists($directorioAbsoluto);
        }

        $logoAnterior = ConfiguracionSistema::valor('logo_sidebar');
        $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension() ?: 'png');
        $nombreArchivo = 'sidebar-logo-' . now()->format('YmdHis') . '-' . str()->random(8) . '.' . $extension;
        $rutaRelativa = $directorioRelativo . '/' . $nombreArchivo;
        $rutaAbsoluta = public_path($rutaRelativa);

        Log::info('LOGO NUEVO', [
            'nombre' => $nombreArchivo,
            'rutaRelativa' => $rutaRelativa,
            'rutaAbsoluta' => $rutaAbsoluta,
        ]);

        $archivo->move(
            $directorioAbsoluto,
            $nombreArchivo
        );

        if (! File::exists($rutaAbsoluta)) {
            throw new RuntimeException(
                'El archivo del logo no quedó guardado.'
            );
        }

        ConfiguracionSistema::query()->updateOrCreate(
            ['clave' => 'logo_sidebar'],
            [
                'valor' => $rutaRelativa,
                'descripcion' => 'Ruta del logo principal mostrado en el sidebar',
            ]
        );

        if ($logoAnterior) {
            $rutaAnterior = public_path($logoAnterior);

            if ($rutaAnterior !== $rutaAbsoluta
                && str_starts_with($logoAnterior, $directorioRelativo . '/')
                && File::exists($rutaAnterior)) {
                Log::info('LOGO ELIMINANDO', [
                    'logoAnterior' => $logoAnterior,
                    'rutaAnterior' => $rutaAnterior,
                    'rutaNueva' => $rutaAbsoluta,
                ]);

                File::delete($rutaAnterior);

                Log::info('LOGO DELETE EJECUTADO');
            }
        }

        return $rutaRelativa;
    }

    private function validarOfrecer(Request $request, bool $actualizacion = false): array
    {
        return $request->validate([
            'titulo' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => [$actualizacion ? 'nullable' : 'required', 'image', 'max:4096'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ]);
    }

    private function procesarImagenOfrecer(UploadedFile $imagen): string
    {
        $origen = @imagecreatefromstring($imagen->get());

        if (! $origen) {
            throw new RuntimeException('No se pudo procesar la imagen cargada.');
        }

        $anchoOrigen = imagesx($origen);
        $altoOrigen = imagesy($origen);
        $maxAncho = 1200;
        $debeRedimensionar = $anchoOrigen > $maxAncho;

        if ($debeRedimensionar) {
            $anchoDestino = $maxAncho;
            $altoDestino = (int) round(($altoOrigen * $anchoDestino) / $anchoOrigen);
            $destino = imagecreatetruecolor($anchoDestino, $altoDestino);

            imagecopyresampled(
                $destino,
                $origen,
                0,
                0,
                0,
                0,
                $anchoDestino,
                $altoDestino,
                $anchoOrigen,
                $altoOrigen
            );
        } else {
            $destino = $origen;
        }

        $mime = strtolower((string) $imagen->getMimeType());
        $extension = 'jpg';
        ob_start();

        if (str_contains($mime, 'png')) {
            $extension = 'png';
            imagepng($destino, null, 6);
        } elseif (str_contains($mime, 'webp') && function_exists('imagewebp')) {
            $extension = 'webp';
            imagewebp($destino, null, 95);
        } else {
            imagejpeg($destino, null, 95);
        }

        $contenido = ob_get_clean();

        imagedestroy($origen);
        if ($debeRedimensionar) {
            imagedestroy($destino);
        }

        $nombreArchivo = now()->format('YmdHis') . '-' . str()->random(10) . '.' . $extension;
        $rutaRelativa = 'ofrecer/' . $nombreArchivo;

        Storage::disk('public')->put($rutaRelativa, $contenido);

        return $rutaRelativa;
    }

    private function normalizarRutaPublica(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }

        $ruta = ltrim($ruta, '/');

        if (str_starts_with($ruta, 'storage/')) {
            $ruta = substr($ruta, strlen('storage/'));
        }

        if (str_starts_with($ruta, 'public/')) {
            $ruta = substr($ruta, strlen('public/'));
        }

        return $ruta;
    }

    private function bancosListado()
    {
        return Banco::query()
            ->withCount('usuarios')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Banco $banco) => [
                'id' => $banco->id,
                'nombre' => $banco->nombre,
                'activo' => (bool) $banco->activo,
                'usuarios_count' => $banco->usuarios_count,
                'can_delete' => $banco->usuarios_count === 0,
            ]);
    }

    private function clavesSistema()
    {
        return ConfiguracionSistema::query()
            ->where('clave', '!=', 'logo_sidebar')
            ->orderBy('clave')
            ->get(['id', 'clave', 'valor', 'descripcion'])
            ->map(fn (ConfiguracionSistema $configuracion) => [
                'id' => $configuracion->id,
                'clave' => $configuracion->clave,
                'nombre' => $this->humanizarClave($configuracion->clave),
                'valor' => $configuracion->valor,
                'descripcion' => $configuracion->descripcion,
            ]);
    }

    private function humanizarClave(string $clave): string
    {
        return str($clave)
            ->replace('_', ' ')
            ->upper()
            ->value();
    }

    private function sectoresActivos()
    {
        return Sector::query()
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'activo', 'orden']);
    }

    private function catalogoActivoPorCategoria(string $categoria)
    {
        return CatalogoOpcion::query()
            ->where('categoria', $categoria)
            ->where('activo', 1)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get(['id', 'categoria', 'nombre', 'valor', 'valor_vinculado', 'valor_freelance', 'orden', 'activo']);
    }


    private function estadoReferidoColores()
    {
        $coloresConfigurados = EstadoReferidoColor::query()
            ->whereIn('estado', array_keys(self::ESTADOS_REFERIDO))
            ->get()
            ->keyBy('estado');

        return collect(self::ESTADOS_REFERIDO)
            ->mapWithKeys(function (string $label, string $estado) use ($coloresConfigurados) {
                $color = $coloresConfigurados->get($estado);
                $default = self::ESTADOS_REFERIDO_DEFAULT_COLORS[$estado];

                return [$estado => [
                    'estado' => $estado,
                    'label' => $label,
                    'bg_color' => $color?->bg_color ?: $default['bg_color'],
                    'text_color' => $color?->text_color ?: $default['text_color'],
                    'activo' => $color ? (bool) $color->activo : false,
                ]];
            });
    }

    private function resolveCategoria(string $slug): string
    {
        abort_unless(array_key_exists($slug, self::CATEGORIAS), 404);

        return self::CATEGORIAS[$slug];
    }
}
