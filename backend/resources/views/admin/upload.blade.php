<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samples uploaden - Order #{{ $songRequest->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8faf6; color: #0d1512; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { font-size: 24px; margin-bottom: 8px; }
        .meta { color: #5b6660; font-size: 14px; margin-bottom: 24px; }
        .card { background: #fff; border: 1px solid #e3e8e1; border-radius: 12px; padding: 24px; margin-bottom: 20px; }
        .sample-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .sample-card { background: #f8faf6; border: 1px solid #e3e8e1; border-radius: 8px; padding: 16px; }
        .sample-card h3 { margin: 0 0 16px; font-size: 16px; }
        label { display: block; font-weight: 500; margin-bottom: 6px; font-size: 14px; }
        input[type="text"], input[type="url"] { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; margin-bottom: 12px; }
        input[type="file"] { margin-bottom: 12px; font-size: 14px; }
        .btn { display: inline-block; background: #16a34a; color: #fff; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #15803d; }
        .btn-secondary { background: #3b82f6; }
        .btn-secondary:hover { background: #2563eb; }
        .btn-group { display: flex; gap: 12px; margin-top: 20px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .info-table { width: 100%; font-size: 14px; }
        .info-table td { padding: 6px 0; vertical-align: top; }
        .info-table td:first-child { color: #5b6660; width: 120px; }
        .lyrics-box { background: #f8faf6; border: 1px solid #e3e8e1; border-radius: 8px; padding: 16px; white-space: pre-wrap; font-size: 14px; max-height: 200px; overflow-y: auto; margin-top: 8px; }
        .existing-sample { display: flex; align-items: center; gap: 12px; padding: 12px; background: #f0fdf4; border-radius: 8px; margin-bottom: 8px; }
        .existing-sample img { width: 48px; height: 48px; border-radius: 6px; object-fit: cover; }
        .existing-sample .info { flex: 1; }
        .existing-sample .title { font-weight: 500; }
        .existing-sample .url { font-size: 12px; color: #5b6660; word-break: break-all; }
        audio { width: 100%; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Samples uploaden</h1>
        <p class="meta">Order #{{ $songRequest->id }} — {{ $songRequest->category_title }} voor {{ $songRequest->recipient_name }}</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="card">
            <h2 style="margin-top:0; font-size:18px;">Order details</h2>
            <table class="info-table">
                <tr><td>Klant e-mail</td><td>{{ $songRequest->email }}</td></tr>
                <tr><td>Suno-titel</td><td>{{ $songRequest->intake['recipientName'] ?? $songRequest->recipient_name }}</td></tr>
                <tr><td>Stijl</td><td>{{ $songRequest->intake['musicStyle'] ?? '—' }}</td></tr>
                <tr><td>Stem</td><td>{{ $songRequest->intake['vocals'] ?? '—' }}</td></tr>
            </table>
            <strong style="display:block; margin-top:16px;">Lyrics</strong>
            <div class="lyrics-box">{!! nl2br(e(str_replace(['\n', '\\n'], "\n", $songRequest->final_lyrics ?: $songRequest->lyrics))) !!}</div>
        </div>

        @if($existingSamples->count() === 4)
            <div class="card">
                <h2 style="margin-top:0; font-size:18px;">Huidige samples</h2>
                @foreach($existingSamples as $sample)
                    <div class="existing-sample">
                        <img src="{{ Storage::disk($sample->storage_disk)->url($sample->cover_path) }}" alt="Cover">
                        <div class="info">
                            <div class="title">{{ $sample->position }}. {{ $sample->title }}</div>
                            <div class="url">{{ $sample->suno_source_url }}</div>
                            <audio controls src="{{ Storage::disk($sample->storage_disk)->url($sample->preview_path) }}"></audio>
                        </div>
                    </div>
                @endforeach

                <form action="{{ route('admin.upload.sendToCustomer', ['token' => $songRequest->admin_upload_token]) }}" method="POST" style="margin-top:20px;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        @if($songRequest->samples_email_sent_at)
                            Opnieuw mailen naar klant
                        @else
                            Mail naar klant versturen
                        @endif
                    </button>
                    @if($songRequest->samples_email_sent_at)
                        <span style="margin-left:12px; color:#5b6660; font-size:14px;">
                            Laatst verstuurd: {{ $songRequest->samples_email_sent_at->format('d-m-Y H:i') }}
                        </span>
                    @endif
                </form>
            </div>
        @endif

        <div class="card">
            <h2 style="margin-top:0; font-size:18px;">
                @if($existingSamples->count() === 4)
                    Samples vervangen
                @else
                    4 Samples uploaden
                @endif
            </h2>

            <form action="{{ route('admin.upload.store', ['token' => $songRequest->admin_upload_token]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="sample-grid">
                    @for($i = 0; $i < 4; $i++)
                        <div class="sample-card">
                            <h3>Sample {{ $i + 1 }}</h3>

                            <label for="title_{{ $i }}">Titel</label>
                            <input type="text" name="samples[{{ $i }}][title]" id="title_{{ $i }}" required
                                value="{{ old("samples.{$i}.title", $existingSamples[$i]->title ?? '') }}">

                            <label for="audio_{{ $i }}">Audio (mp3/wav)</label>
                            <input type="file" name="samples[{{ $i }}][audio]" id="audio_{{ $i }}" accept=".mp3,.wav,.m4a" {{ $existingSamples->count() !== 4 ? 'required' : '' }}>

                            <label for="cover_{{ $i }}">Cover (jpg/png)</label>
                            <input type="file" name="samples[{{ $i }}][cover]" id="cover_{{ $i }}" accept=".jpg,.jpeg,.png" {{ $existingSamples->count() !== 4 ? 'required' : '' }}>

                            <label for="suno_url_{{ $i }}">Suno URL</label>
                            <input type="url" name="samples[{{ $i }}][suno_url]" id="suno_url_{{ $i }}" required placeholder="https://suno.com/song/..."
                                value="{{ old("samples.{$i}.suno_url", $existingSamples[$i]->suno_source_url ?? '') }}">
                        </div>
                    @endfor
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn">Samples opslaan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
