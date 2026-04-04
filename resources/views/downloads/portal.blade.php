<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Photo Booth</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #0f172a;
            --bg-soft: #111827;
            --card: rgba(15, 23, 42, 0.72);
            --card-border: rgba(255, 255, 255, 0.12);
            --text: #e5e7eb;
            --muted: #94a3b8;
            --accent: #22c55e;
            --accent-2: #38bdf8;
            --danger: #f97316;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.24), transparent 28%),
                radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.22), transparent 24%),
                linear-gradient(160deg, #020617 0%, #0f172a 42%, #111827 100%);
            color: var(--text);
        }

        .wrap {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0 56px;
        }

        .hero {
            display: grid;
            gap: 20px;
            grid-template-columns: 1.3fr 0.7fr;
            align-items: end;
            padding: 28px;
            border: 1px solid var(--card-border);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.84), rgba(15, 23, 42, 0.58));
            backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.24);
            color: #bbf7d0;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        h1 {
            margin: 16px 0 12px;
            font-size: clamp(2rem, 5vw, 4.2rem);
            line-height: 0.96;
            letter-spacing: -0.05em;
        }

        .lead {
            max-width: 60ch;
            margin: 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .stats {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stat {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--card-border);
        }

        .stat .value {
            display: block;
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .stat .label {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
        }

        .section {
            margin-top: 28px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }

        .section-title {
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.02em;
        }

        .section-note {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .card {
            overflow: hidden;
            border-radius: 24px;
            background: var(--card);
            border: 1px solid var(--card-border);
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.18);
        }

        .preview {
            aspect-ratio: 1 / 1;
            background: rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .preview img,
        .preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .placeholder {
            padding: 24px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .content {
            padding: 18px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.22);
            color: #bae6fd;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .file-name {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 600;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.18s ease, opacity 0.18s ease, background 0.18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #06250f;
            background: linear-gradient(135deg, #4ade80, #22c55e);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
        }

        .footer {
            margin-top: 20px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .wrap {
                width: min(100% - 20px, 1180px);
                padding-top: 20px;
            }

            .hero {
                padding: 20px;
                border-radius: 22px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <main class="wrap">
        <section class="hero">
            <div>
                <span class="eyebrow">Download Center</span>
                <h1>Pilih file yang mau diunduh.</h1>
                <p class="lead">
                    Halaman ini menampilkan template, foto hasil yang tersedia untuk sesi ini.
                    User bisa download satu per satu sesuai kebutuhan, atau ambil semuanya sekaligus lewat ZIP.
                </p>
            </div>

            <div class="stats">
                <div class="stat">
                    <span class="value">{{ count($downloadItems) }}</span>
                    <span class="label">file tersedia</span>
                </div>
                <div class="stat">
                    <span
                        class="value">{{ $photo->expired_at ? $photo->expired_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}</span>
                    <span class="label">Expired</span>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-head">
                <div>
                    <h2 class="section-title">File tersedia</h2>
                    <p class="section-note">Klik tombol download di tiap kartu kalau user hanya ingin mengambil file
                        tertentu.</p>
                </div>
                <a class="btn btn-primary" href="{{ $archiveUrl }}">
                    Download semua ZIP
                </a>
            </div>

            <div class="grid">
                @foreach ($downloadItems as $item)
                    <article class="card">
                        <div class="preview">
                            @if ($item['type'] === 'video')
                                <video controls playsinline preload="metadata">
                                    <source src="{{ $item['url'] }}">
                                </video>
                            @elseif (in_array(strtolower(pathinfo($item['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
                                <img src="{{ $item['url'] }}" alt="{{ $item['label'] }}">
                            @else
                                <div class="placeholder">
                                    Preview tidak tersedia.<br>
                                    File tetap bisa diunduh.
                                </div>
                            @endif
                        </div>
                        <div class="content">
                            <span class="badge">{{ $item['label'] }}</span>
                            <p class="file-name">{{ $item['name'] }}</p>
                            <div class="actions">
                                <a class="btn btn-primary" href="{{ $item['download_url'] }}">Download</a>
                                <a class="btn btn-secondary" href="{{ $item['url'] }}" target="_blank"
                                    rel="noopener">Buka</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <p class="footer">
            Jika ada file yang tidak tampil, biasanya formatnya tidak didukung oleh preview browser, tetapi tetap bisa
            diunduh.
        </p>
    </main>
</body>

</html>
