<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Token Access Console</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|instrument-sans:400,500,600"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [data-hidden="true"] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100 antialiased">
    <div
        class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_28%),radial-gradient(circle_at_top_right,rgba(251,146,60,0.18),transparent_24%),radial-gradient(circle_at_bottom,rgba(15,118,110,0.2),transparent_34%)]">
    </div>
    <div
        class="fixed inset-0 -z-10 bg-[linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-size-[72px_72px] mask-[linear-gradient(to_bottom,rgba(0,0,0,0.7),transparent_92%)]">
    </div>

    <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <section
            class="rounded-4xl border border-white/10 bg-white/5 p-5 shadow-2xl shadow-emerald-950/20 backdrop-blur-xl sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium tracking-[0.22em] text-emerald-200 uppercase">
                        Token Access Console
                    </div>
                    <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-white sm:text-5xl">
                        Login, generate, dan kelola token dalam satu dashboard.
                    </h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                        Dashboard ini memakai token login admin untuk mengamankan aksi generate, melihat daftar token,
                        menonaktifkan, dan menghapus token. Token disimpan hashed di database.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:min-w-90 lg:grid-cols-1">
                    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</p>
                        <p id="authStatus" class="mt-2 text-sm font-medium text-emerald-300">Belum login</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tokens</p>
                        <p id="tokenCountLabel" class="mt-2 text-sm font-medium text-white">0 token</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Mode</p>
                        <p class="mt-2 text-sm font-medium text-white">Single atau multi device</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[360px_minmax(0,1fr)]">
            <div class="space-y-6">
                <article id="loginCard"
                    class="rounded-4xl border border-white/10 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/30 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Access</p>
                            <h2 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-white">Admin login</h2>
                        </div>
                        <div
                            class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 text-xs text-emerald-200">
                            Protected</div>
                    </div>

                    <form id="loginForm" class="mt-6 space-y-4">
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
                            <span>Login</span>
                        </button>
                    </form>

                    <div id="loginMessage"
                        class="mt-4 hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    </div>
                </article>

                <article id="sessionCard" data-hidden="true"
                    class="rounded-4xl border border-white/10 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/30 backdrop-blur-xl">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Session</p>
                    <h2 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-white">Admin aktif</h2>
                    <p id="userLabel" class="mt-3 text-sm leading-6 text-slate-300"></p>
                    <div class="mt-5 flex gap-3">
                        <button id="refreshButton" type="button"
                            class="inline-flex flex-1 items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/10">
                            Refresh data
                        </button>
                        <button id="logoutButton" type="button"
                            class="inline-flex flex-1 items-center justify-center rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm font-medium text-rose-200 transition hover:bg-rose-400/15">
                            Logout
                        </button>
                    </div>
                </article>

                <article
                    class="rounded-4xl border border-white/10 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/30 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Generate</p>
                            <h2 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-white">Token baru</h2>
                        </div>
                        <span
                            class="rounded-2xl border border-amber-400/20 bg-amber-400/10 px-3 py-2 text-xs text-amber-200">Secure
                            SHA-256 stored</span>
                    </div>

                    <form id="generateForm" class="mt-6 space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="tokenName">Nama
                                token</label>
                            <input id="tokenName" type="text"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-emerald-400/60"
                                placeholder="Contoh: Booth Event 2026">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-200" for="tokenMode">Mode
                                    akses</label>
                                <select id="tokenMode"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition focus:border-emerald-400/60">
                                    <option value="single">1 akun / 1 device</option>
                                    <option value="multi">Multipel device</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-200" for="maxDevices">Max
                                    device</label>
                                <input id="maxDevices" type="number" min="1" value="1"
                                    class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-emerald-400/60">
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-200" for="expiredHours">Expired
                                dalam jam</label>
                            <input id="expiredHours" type="number" min="1" value="24"
                                class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-emerald-400/60">
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-orange-400 to-amber-500 px-4 py-3 text-sm font-semibold text-slate-950 transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-orange-300/50">
                            Generate token
                        </button>
                    </form>

                    <div id="generateMessage"
                        class="mt-4 hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    </div>
                </article>

                <article id="newTokenCard" data-hidden="true"
                    class="rounded-4xl border border-emerald-400/20 bg-emerald-400/10 p-6 shadow-xl shadow-emerald-950/20 backdrop-blur-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-emerald-200">Token baru</p>
                            <h2 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-white">Token siap dipakai
                            </h2>
                        </div>
                        <button id="copyTokenButton" type="button"
                            class="rounded-2xl border border-white/10 bg-white/10 px-3 py-2 text-xs font-medium text-white transition hover:bg-white/15">Copy</button>
                    </div>
                    <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Plain token</p>
                        <p id="generatedTokenValue" class="mt-2 break-all font-mono text-sm text-emerald-200"></p>
                    </div>
                    <p id="generatedTokenMeta" class="mt-4 text-sm leading-6 text-emerald-50/90"></p>
                </article>
            </div>

            <article
                class="rounded-4xl border border-white/10 bg-slate-900/75 p-5 shadow-xl shadow-slate-950/30 backdrop-blur-xl sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Index</p>
                        <h2 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-white">Daftar token</h2>
                    </div>
                    <div class="flex w-full flex-col gap-3 md:max-w-md md:flex-row">
                        <input id="searchInput" type="search"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-emerald-400/60"
                            placeholder="Cari nama token atau ID">
                        <button id="reloadTokens" type="button"
                            class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-white transition hover:border-white/20 hover:bg-white/10">Muat
                            ulang</button>
                    </div>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total</p>
                        <p id="statTotal" class="mt-2 text-2xl font-semibold text-white">0</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-200">Aktif</p>
                        <p id="statActive" class="mt-2 text-2xl font-semibold text-emerald-100">0</p>
                    </div>
                    <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-rose-200">Nonaktif</p>
                        <p id="statInactive" class="mt-2 text-2xl font-semibold text-rose-100">0</p>
                    </div>
                    <div class="rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-amber-200">Expired</p>
                        <p id="statExpired" class="mt-2 text-2xl font-semibold text-amber-100">0</p>
                    </div>
                </div>

                <div id="tokensEmpty"
                    class="mt-6 rounded-3xl border border-dashed border-white/10 bg-white/5 p-8 text-center text-sm text-slate-400">
                    Belum ada token. Login lalu generate token pertama dari panel kiri.
                </div>

                <div id="tokensList" class="mt-6 space-y-3"></div>
            </article>
        </section>
    </main>

    <div id="toast"
        class="pointer-events-none fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-2xl border border-white/10 bg-slate-950/95 px-4 py-3 text-sm text-slate-100 shadow-2xl shadow-slate-950/40 backdrop-blur-xl">
    </div>

    <script>
        // Check if user is logged in, redirect to login if not
        const authToken = localStorage.getItem('photobooth_admin_token');
        if (!authToken) {
            window.location.href = '/login';
        }

        const state = {
            authToken: authToken || '',
            user: null,
            tokens: [],
            generatedToken: '',
        };

        const els = {
            authStatus: document.getElementById('authStatus'),
            tokenCountLabel: document.getElementById('tokenCountLabel'),
            loginCard: document.getElementById('loginCard'),
            sessionCard: document.getElementById('sessionCard'),
            userLabel: document.getElementById('userLabel'),
            loginForm: document.getElementById('loginForm'),
            loginMessage: document.getElementById('loginMessage'),
            email: document.getElementById('email'),
            password: document.getElementById('password'),
            generateForm: document.getElementById('generateForm'),
            generateMessage: document.getElementById('generateMessage'),
            tokenName: document.getElementById('tokenName'),
            tokenMode: document.getElementById('tokenMode'),
            maxDevices: document.getElementById('maxDevices'),
            expiredHours: document.getElementById('expiredHours'),
            newTokenCard: document.getElementById('newTokenCard'),
            generatedTokenValue: document.getElementById('generatedTokenValue'),
            generatedTokenMeta: document.getElementById('generatedTokenMeta'),
            copyTokenButton: document.getElementById('copyTokenButton'),
            tokensList: document.getElementById('tokensList'),
            tokensEmpty: document.getElementById('tokensEmpty'),
            searchInput: document.getElementById('searchInput'),
            reloadTokens: document.getElementById('reloadTokens'),
            refreshButton: document.getElementById('refreshButton'),
            logoutButton: document.getElementById('logoutButton'),
            statTotal: document.getElementById('statTotal'),
            statActive: document.getElementById('statActive'),
            statInactive: document.getElementById('statInactive'),
            statExpired: document.getElementById('statExpired'),
            toast: document.getElementById('toast'),
        };

        const toJakarta = (value) => {
            if (!value) {
                return '-';
            }

            return new Intl.DateTimeFormat('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short',
                timeZone: 'Asia/Jakarta',
            }).format(new Date(value));
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

            if (state.authToken && options.auth !== false) {
                headers.Authorization = `Bearer ${state.authToken}`;
            }

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

        const renderStats = () => {
            const total = state.tokens.length;
            const active = state.tokens.filter((token) => token.is_active && !token.is_expired).length;
            const inactive = state.tokens.filter((token) => !token.is_active).length;
            const expired = state.tokens.filter((token) => token.is_expired).length;

            els.statTotal.textContent = total;
            els.statActive.textContent = active;
            els.statInactive.textContent = inactive;
            els.statExpired.textContent = expired;
            els.tokenCountLabel.textContent = `${total} token`;
        };

        const renderTokens = () => {
            const query = els.searchInput.value.trim().toLowerCase();
            const filtered = state.tokens.filter((token) => {
                const searchable = [token.id, token.name, token.max_devices, token.device_count].join(' ')
                    .toLowerCase();
                return searchable.includes(query);
            });

            els.tokensList.innerHTML = '';
            els.tokensEmpty.classList.toggle('hidden', filtered.length > 0);

            if (!filtered.length) {
                renderStats();
                return;
            }

            const fragment = document.createDocumentFragment();

            filtered.forEach((token) => {
                const isExpired = Boolean(token.is_expired);
                const isActive = Boolean(token.is_active) && !isExpired;
                const statusLabel = isExpired ? 'Expired' : isActive ? 'Aktif' : 'Nonaktif';
                const statusClass = isExpired ?
                    'border-amber-400/20 bg-amber-400/10 text-amber-200' :
                    isActive ?
                    'border-emerald-400/20 bg-emerald-400/10 text-emerald-200' :
                    'border-rose-400/20 bg-rose-400/10 text-rose-200';

                const card = document.createElement('div');
                card.className =
                    'rounded-[1.5rem] border border-white/10 bg-slate-950/60 p-4 transition hover:border-white/20 hover:bg-slate-950/75';
                card.innerHTML = `
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-['Space_Grotesk'] text-lg font-semibold text-white">${token.name || 'Untitled token'}</h3>
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[11px] uppercase tracking-[0.18em] text-slate-300">#${token.id}</span>
                                <span class="rounded-full border px-3 py-1 text-[11px] uppercase tracking-[0.18em] ${statusClass}">${statusLabel}</span>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Akses</p>
                                    <p class="mt-1 text-sm text-white">${token.device_count} / ${token.max_devices} device</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Expired</p>
                                    <p class="mt-1 text-sm text-white">${toJakarta(token.expired_at)}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Device list</p>
                                    <p class="mt-1 text-sm text-white">${(token.devices || []).length} terdaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 xl:justify-end">
                            <button data-action="deactivate" data-id="${token.id}" class="rounded-2xl border border-amber-400/20 bg-amber-400/10 px-4 py-2 text-sm font-medium text-amber-200 transition hover:bg-amber-400/15">Nonaktifkan</button>
                            <button data-action="delete" data-id="${token.id}" class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-2 text-sm font-medium text-rose-200 transition hover:bg-rose-400/15">Hapus</button>
                        </div>
                    </div>
                `;

                fragment.appendChild(card);
            });

            els.tokensList.appendChild(fragment);
            renderStats();
        };

        const loadUserAndTokens = async () => {
            if (!state.authToken) {
                state.user = null;
                els.authStatus.textContent = 'Belum login';
                els.sessionCard.dataset.hidden = 'true';
                els.loginCard.dataset.hidden = 'false';
                els.generateForm.querySelector('button[type="submit"]').disabled = true;
                return;
            }

            try {
                const user = await request('/api/user');
                state.user = user;
                els.authStatus.textContent = 'Login aktif';
                els.userLabel.textContent = `${user.name} • ${user.email}`;
                els.loginCard.dataset.hidden = 'true';
                els.sessionCard.dataset.hidden = 'false';
                els.generateForm.querySelector('button[type="submit"]').disabled = false;
                await loadTokens();
            } catch (error) {
                state.authToken = '';
                localStorage.removeItem('photobooth_admin_token');
                els.authStatus.textContent = 'Belum login';
                els.loginCard.dataset.hidden = 'false';
                els.sessionCard.dataset.hidden = 'true';
                els.newTokenCard.dataset.hidden = 'true';
                state.generatedToken = '';
                els.generatedTokenValue.textContent = '';
                els.generatedTokenMeta.textContent = '';
                els.generateForm.querySelector('button[type="submit"]').disabled = true;
                showToast('Session login habis. Silakan login ulang.', 'error');
            }
        };

        const loadTokens = async () => {
            if (!state.authToken) {
                state.tokens = [];
                renderTokens();
                return;
            }

            try {
                state.tokens = await request('/api/tokens');
                renderTokens();
            } catch (error) {
                showToast(error.message, 'error');
            }
        };

        els.loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            setMessage(els.loginMessage, '');

            try {
                const payload = await request('/api/login', {
                    method: 'POST',
                    auth: false,
                    body: {
                        email: els.email.value,
                        password: els.password.value,
                    },
                });

                state.authToken = payload.access_token;
                localStorage.setItem('photobooth_admin_token', payload.access_token);
                setMessage(els.loginMessage, 'Login berhasil.', 'success');
                showToast('Login berhasil.', 'success');
                await loadUserAndTokens();
            } catch (error) {
                setMessage(els.loginMessage, error.message, 'error');
            }
        });

        els.generateForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            setMessage(els.generateMessage, '');

            if (!state.authToken) {
                setMessage(els.generateMessage, 'Silakan login dulu.', 'error');
                return;
            }

            const mode = els.tokenMode.value;
            const maxDevices = mode === 'single' ? 1 : Number(els.maxDevices.value || 1);

            try {
                const payload = await request('/api/token/generate', {
                    method: 'POST',
                    body: {
                        name: els.tokenName.value || null,
                        max_devices: maxDevices,
                        expired_hours: Number(els.expiredHours.value || 24),
                    },
                });

                state.generatedToken = payload.token;
                els.generatedTokenValue.textContent = payload.token;
                els.generatedTokenMeta.textContent =
                    `Token baru dibuat dan aktif sampai ${payload.expires_at}. Simpan token ini sekarang, karena plain token hanya muncul sekali.`;
                els.newTokenCard.dataset.hidden = 'false';
                setMessage(els.generateMessage, 'Token berhasil dibuat.', 'success');
                showToast('Token baru berhasil dibuat.', 'success');
                els.generateForm.reset();
                els.tokenMode.value = 'single';
                els.maxDevices.value = '1';
                await loadTokens();
            } catch (error) {
                setMessage(els.generateMessage, error.message, 'error');
            }
        });

        els.copyTokenButton.addEventListener('click', async () => {
            if (!state.generatedToken) {
                return;
            }

            await navigator.clipboard.writeText(state.generatedToken);
            showToast('Token disalin ke clipboard.', 'success');
        });

        els.searchInput.addEventListener('input', renderTokens);
        els.reloadTokens.addEventListener('click', loadTokens);
        els.refreshButton.addEventListener('click', loadUserAndTokens);
        els.logoutButton.addEventListener('click', async () => {
            try {
                await request('/api/logout', {
                    method: 'POST'
                });
            } catch (error) {
                // Token sudah mungkin tidak valid. Tetap lanjut bersih-bersih state.
            }

            state.authToken = '';
            state.user = null;
            localStorage.removeItem('photobooth_admin_token');
            els.tokensList.innerHTML = '';
            state.tokens = [];
            els.newTokenCard.dataset.hidden = 'true';
            state.generatedToken = '';
            els.generatedTokenValue.textContent = '';
            els.generatedTokenMeta.textContent = '';
            renderTokens();
            await loadUserAndTokens();
            showToast('Logout berhasil.', 'success');
        });

        els.tokenMode.addEventListener('change', () => {
            const isSingle = els.tokenMode.value === 'single';
            els.maxDevices.value = isSingle ? '1' : els.maxDevices.value || '2';
            els.maxDevices.disabled = isSingle;
            els.maxDevices.classList.toggle('opacity-60', isSingle);
        });

        els.maxDevices.disabled = true;
        els.generateForm.querySelector('button[type="submit"]').disabled = !state.authToken;

        els.tokensList.addEventListener('click', async (event) => {
            const button = event.target.closest('button[data-action]');
            if (!button) {
                return;
            }

            const id = button.dataset.id;
            const action = button.dataset.action;
            const token = state.tokens.find((item) => String(item.id) === String(id));

            if (!token) {
                return;
            }

            if (action === 'deactivate') {
                if (!confirm(`Nonaktifkan token ${token.name || token.id}?`)) {
                    return;
                }

                try {
                    await request(`/api/token/${id}/deactivate`, {
                        method: 'POST'
                    });
                    showToast('Token berhasil dinonaktifkan.', 'success');
                    await loadTokens();
                } catch (error) {
                    showToast(error.message, 'error');
                }
                return;
            }

            if (action === 'delete') {
                if (!confirm(`Hapus token ${token.name || token.id}? Aksi ini permanen.`)) {
                    return;
                }

                try {
                    await request(`/api/token/${id}`, {
                        method: 'DELETE'
                    });
                    showToast('Token berhasil dihapus.', 'success');
                    await loadTokens();
                } catch (error) {
                    showToast(error.message, 'error');
                }
            }
        });

        loadUserAndTokens();
    </script>
</body>

</html>
