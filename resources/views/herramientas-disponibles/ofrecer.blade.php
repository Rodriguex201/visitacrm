@extends('layouts.app')

@section('content')
<div
    x-data="{
        imageOpen: false,
        imagen: '',
        sharePageUrl: @js(route('herramientas.ofrecer')),
        openImageModal(imageUrl) {
            this.imagen = imageUrl || ''
            this.imageOpen = Boolean(this.imagen)
        },
        closeImageModal() {
            this.imageOpen = false
            this.imagen = ''
        },
        shareOffer(item) {
            const title = (item?.titulo || '').trim();
            const description = this.summarizeDescription((item?.descripcion || '').trim());
            const imageUrl = this.getPublicImageUrl(item?.imagen_url);
            const videoUrl = this.getPublicAssetUrl(item?.video_url);
            const shareText = this.buildShareText({ title, description, imageUrl, videoUrl });

            window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank', 'noopener,noreferrer');
        },
        buildShareText({ title, description, imageUrl, videoUrl }) {
            const parts = [
                'Hola.',
                '',
                'Quiero compartirte una de nuestras soluciones de RM Soft.',
                '',
            ];

            if (title) {
                parts.push(`*${title}*`, '');
            }

            if (description) {
                parts.push(description, '');
            }

            if (imageUrl) {
                parts.push('Imagen:', imageUrl, '');
            }

            if (videoUrl) {
                parts.push('Ver demostracion:', videoUrl, '');
            }

            parts.push('Si deseas mas informacion, con gusto podemos ayudarte.');

            return parts.join('\n');
        },
        summarizeDescription(description) {
            if (!description) {
                return '';
            }

            const normalized = description.replace(/\s+/g, ' ').trim();

            if (normalized.length <= 280) {
                return normalized;
            }

            return `${normalized.slice(0, 277).trimEnd()}...`;
        },
        getPublicImageUrl(rawUrl) {
            return this.getPublicAssetUrl(rawUrl);
        },
        getPublicAssetUrl(rawUrl) {
            if (!rawUrl || typeof rawUrl !== 'string') {
                return null;
            }

            try {
                const parsed = new URL(rawUrl, window.location.origin);

                if (!['http:', 'https:'].includes(parsed.protocol)) {
                    return null;
                }

                if (!parsed.hostname || parsed.hostname === 'localhost' || parsed.hostname === '127.0.0.1') {
                    return null;
                }

                return parsed.toString();
            } catch (error) {
                return null;
            }
        }
    }"
>
    <section class="space-y-6">
        <header class="rounded-3xl border border-slate-200 bg-gradient-to-r from-sky-50 via-white to-indigo-50 p-6 shadow-sm md:p-8">
            <h1 class="text-2xl font-bold text-slate-950 md:text-3xl">Qu&eacute; puedo ofrecer</h1>
            <p class="mt-1 text-sm text-slate-600 md:text-base">Galer&iacute;a de soluciones y servicios disponibles.</p>
        </header>

        @if ($ofrecerItems->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                A&uacute;n no hay elementos activos para ofrecer.
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach ($ofrecerItems as $item)
                    <article class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-black/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                        @if ($item->imagen)
                            <img
                                src="{{ $item->imagen_url }}"
                                alt="Imagen ofrecer"
                                class="h-64 w-full cursor-pointer rounded-xl bg-white object-contain"
                                @click="openImageModal('{{ $item->imagen_url }}')"
                            >
                        @endif

                        <div class="space-y-3 p-4">
                            <div class="flex items-start justify-between gap-3">
                                @if ($item->titulo)
                                    <h3 class="text-base font-semibold text-slate-900">{{ $item->titulo }}</h3>
                                @endif

                                <button
                                    type="button"
                                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:bg-emerald-100 hover:text-emerald-700"
                                    @click="shareOffer(@js([
                                        'titulo' => $item->titulo,
                                        'descripcion' => $item->descripcion,
                                        'imagen_url' => $item->imagen_url,
                                        'video_url' => $item->video_url,
                                    ]))"
                                    aria-label="Compartir por WhatsApp"
                                    title="Compartir por WhatsApp"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M19.05 4.91A9.82 9.82 0 0 0 12.03 2C6.56 2 2.1 6.46 2.1 11.93c0 1.75.46 3.46 1.32 4.97L2 22l5.24-1.37a9.9 9.9 0 0 0 4.79 1.22h.01c5.47 0 9.93-4.46 9.93-9.93a9.86 9.86 0 0 0-2.92-7.01ZM12.04 20.2h-.01a8.23 8.23 0 0 1-4.19-1.15l-.3-.18-3.11.81.83-3.03-.2-.31a8.24 8.24 0 0 1-1.27-4.41c0-4.55 3.7-8.26 8.25-8.26 2.2 0 4.26.86 5.82 2.42a8.2 8.2 0 0 1 2.42 5.83c0 4.55-3.7 8.25-8.24 8.25Zm4.53-6.16c-.25-.12-1.47-.73-1.7-.81-.23-.08-.4-.12-.56.12-.17.25-.65.81-.8.98-.15.17-.3.19-.56.06-.25-.12-1.08-.4-2.05-1.28-.76-.68-1.27-1.52-1.42-1.77-.15-.25-.02-.38.11-.51.11-.11.25-.3.37-.46.12-.15.16-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.48-.4-.41-.56-.41h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1s.9 2.43 1.02 2.6c.12.17 1.77 2.7 4.28 3.78.6.26 1.07.42 1.44.53.6.19 1.14.16 1.57.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29Z"/>
                                    </svg>
                                </button>
                            </div>

                            @if ($item->descripcion)
                                <p class="text-sm text-slate-600">{{ $item->descripcion }}</p>
                            @endif

                            @if ($item->has_demo_video)
                                <a
                                    href="{{ $item->video_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                >
                                    <span class="text-xs leading-none">&#9654;</span>
                                    <span>Ver demostracion</span>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div
        x-show="imageOpen"
        x-transition
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
        @click.self="closeImageModal()"
        @keydown.escape.window="closeImageModal()"
    >
        <div class="relative w-full max-w-5xl p-4">
            <button
                type="button"
                @click="closeImageModal()"
                class="absolute right-6 top-6 rounded-full bg-white px-3 py-1 shadow"
            >
                &times;
            </button>
            <img
                :src="imagen"
                class="max-h-[90vh] w-full rounded-lg bg-white object-contain"
            >
        </div>
    </div>
</div>
@endsection
