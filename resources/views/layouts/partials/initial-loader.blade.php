<style>
    html.app-loading,
    html.app-loading body {
        overflow: hidden;
    }

    #app-loader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at top, rgba(59, 130, 246, 0.18), transparent 42%),
            linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        opacity: 1;
        visibility: visible;
        transition: opacity 320ms ease, visibility 320ms ease;
    }

    #app-loader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .app-loader__panel {
        display: flex;
        min-width: 220px;
        flex-direction: column;
        align-items: center;
        gap: 0.9rem;
        padding: 1.5rem;
        text-align: center;
        color: #0f172a;
    }

    .app-loader__mark {
        width: 3.5rem;
        height: 3.5rem;
        border: 4px solid rgba(37, 99, 235, 0.18);
        border-top-color: #2563eb;
        border-radius: 9999px;
        animation: app-loader-spin 0.8s linear infinite;
    }

    .app-loader__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .app-loader__text {
        margin: 0;
        font-size: 0.92rem;
        color: #475569;
    }

    [data-app-shell] {
        opacity: 0;
        visibility: hidden;
        transition: opacity 220ms ease;
    }

    html.app-loaded [data-app-shell] {
        opacity: 1;
        visibility: visible;
    }

    @keyframes app-loader-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #app-loader,
        [data-app-shell] {
            transition: none;
        }

        .app-loader__mark {
            animation-duration: 1.6s;
        }
    }
</style>

<script>
    document.documentElement.classList.add('app-loading');

    window.addEventListener('load', () => {
        const root = document.documentElement;
        const loader = document.getElementById('app-loader');

        root.classList.add('app-loaded');
        root.classList.remove('app-loading');

        if (!loader) {
            return;
        }

        loader.classList.add('is-hidden');
        loader.addEventListener('transitionend', () => loader.remove(), { once: true });
    }, { once: true });
</script>

<div id="app-loader" role="status" aria-live="polite" aria-label="Cargando aplicación">
    <div class="app-loader__panel">
        <div class="app-loader__mark" aria-hidden="true"></div>
        <p class="app-loader__title">{{ config('app.name', 'VisitaCRM') }}</p>
        <p class="app-loader__text">Cargando recursos iniciales...</p>
    </div>
</div>

<noscript>
    <style>
        html body [data-app-shell] {
            opacity: 1 !important;
            visibility: visible !important;
        }

        #app-loader {
            display: none !important;
        }
    </style>
</noscript>
