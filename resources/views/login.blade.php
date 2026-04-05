<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Token Access Console</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|instrument-sans:400,500,600"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100 antialiased">
    <div
        class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_28%),radial-gradient(circle_at_top_right,rgba(251,146,60,0.18),transparent_24%),radial-gradient(circle_at_bottom,rgba(15,118,110,0.2),transparent_34%)]">
    </div>
    <div
        class="fixed inset-0 -z-10 bg-[linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-size-[72px_72px] mask-[linear-gradient(to_bottom,rgba(0,0,0,0.7),transparent_92%)]">
    </div>

    <main class="mx-auto flex min-h-screen w-full items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium tracking-[0.22em] text-emerald-200 uppercase">
                    Token Access Console
                </div>
                <h1 class="font-['Space_Grotesk'] text-4xl font-bold tracking-tight text-white">
                    Admin Login
                </h1>
                <p class="mt-3 text-sm leading-6 text-slate-300">
                    Masukkan kredensial admin untuk mengakses console token management
                </p>
            </div>

            <article
                class="rounded-4xl border border-white/10 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/30 backdrop-blur-xl">
                <form id="loginForm" class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="email">Email</label>
                        <input id="email" type="email" required
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none ring-0 transition placeholder:text-slate-500 focus:border-emerald-400/60 focus:bg-slate-950"
                            placeholder="admin@domain.com">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="password">Password</label>
                        <input id="password" type="password" required
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none ring-0 transition placeholder:text-slate-500 focus:border-emerald-400/60 focus:bg-slate-950"
                            placeholder="••••••••">
                    </div>
                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-emerald-300/50">
                        <span id="buttonText">Login</span>
                    </button>
                </form>

                <div id="loginMessage"
                    class="mt-4 hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                </div>
            </article>

            <p class="mt-6 text-center text-xs text-slate-400">
                Token Access Console • Secure Authentication Powered by Sanctum
            </p>
        </div>
    </main>

    <div id="toast"
        class="pointer-events-none fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-2xl border border-white/10 bg-slate-950/95 px-4 py-3 text-sm text-slate-100 shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
    </div>

    <script>
        const els = {
            loginForm: document.getElementById('loginForm'),
            loginMessage: document.getElementById('loginMessage'),
            email: document.getElementById('email'),
            password: document.getElementById('password'),
            buttonText: document.getElementById('buttonText'),
            toast: document.getElementById('toast'),
        };

        const showToast = (message, tone = 'info') => {
            const toneClass = tone === 'error' ?
                'border-rose-400/20 bg-rose-500/10 text-rose-100' :
                tone === 'success' ?
                'border-emerald-400/20 bg-emerald-500/10 text-emerald-100' :
                'border-white/10 bg-slate-950/95 text-slate-100';

            els.toast.className =
                `pointer-events-none fixed bottom-5 right-5 z-50 max-w-sm rounded-2xl border px-4 py-3 text-sm shadow-2xl shadow-slate-950/40 backdrop-blur-xl ${toneClass}`;
            els.toast.textContent = message;
            els.toast.classList.remove('hidden');
            clearTimeout(showToast.timer);
            showToast.timer = window.setTimeout(() => els.toast.classList.add('hidden'), 2500);
        };

        const setMessage = (el, message, tone = 'info') => {
            if (!message) {
                el.classList.add('hidden');
                el.textContent = '';
                return;
            }

            const toneClass = tone === 'error' ?
                'border-rose-400/20 bg-rose-500/10 text-rose-100' :
                tone === 'success' ?
                'border-emerald-400/20 bg-emerald-500/10 text-emerald-100' :
                'border-white/10 bg-white/5 text-slate-200';

            el.className = `mt-4 rounded-2xl border px-4 py-3 text-sm ${toneClass}`;
            el.textContent = message;
            el.classList.remove('hidden');
        };

        const request = async (path, options = {}) => {
            const headers = {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            };

            const response = await fetch(path, {
                ...options,
                headers,
                body: options.body ? JSON.stringify(options.body) : undefined,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const message = payload.message || payload.error || 'Request gagal';
                throw new Error(message);
            }

            return payload;
        };

        els.loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            setMessage(els.loginMessage, '');

            const button = els.loginForm.querySelector('button[type="submit"]');
            button.disabled = true;
            els.buttonText.textContent = 'Logging in...';

            try {
                const payload = await request('/api/login', {
                    method: 'POST',
                    body: {
                        email: els.email.value,
                        password: els.password.value,
                    },
                });

                localStorage.setItem('photobooth_admin_token', payload.access_token);
                setMessage(els.loginMessage, 'Login berhasil, redirecting...', 'success');
                showToast('Login berhasil!', 'success');

                setTimeout(() => {
                    window.location.href = '/token-dashboard';
                }, 500);
            } catch (error) {
                button.disabled = false;
                els.buttonText.textContent = 'Login';
                setMessage(els.loginMessage, error.message, 'error');
                showToast(error.message, 'error');
            }
        });
    </script>
</body>

</html>
