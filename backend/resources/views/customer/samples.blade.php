<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kies je nummer - Voor Ieder Moment</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); color: #0d1512; margin: 0; padding: 20px; min-height: 100vh; }
        .container { max-width: 700px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 32px; }
        .logo { font-size: 24px; font-weight: 700; color: #16a34a; margin-bottom: 4px; }
        h1 { font-size: 22px; margin: 0 0 8px; }
        .subtitle { color: #5b6660; font-size: 14px; }
        .samples-grid { display: grid; gap: 16px; }
        .sample-card { background: #fff; border: 2px solid #e3e8e1; border-radius: 16px; padding: 20px; display: flex; gap: 16px; cursor: pointer; transition: all 0.2s; }
        .sample-card:hover { border-color: #16a34a; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .sample-card.selected { border-color: #16a34a; background: #f0fdf4; }
        .sample-card input[type="radio"] { display: none; }
        .cover { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .sample-info { flex: 1; min-width: 0; }
        .sample-number { font-size: 12px; color: #5b6660; margin-bottom: 4px; }
        .sample-title { font-weight: 600; font-size: 16px; margin-bottom: 8px; }
        audio { width: 100%; height: 40px; }
        .actions { margin-top: 24px; text-align: center; }
        .btn { display: inline-block; background: #16a34a; color: #fff; padding: 16px 48px; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #15803d; }
        .btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .expires { text-align: center; color: #5b6660; font-size: 13px; margin-top: 16px; }
        .modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: #fff; border-radius: 16px; padding: 32px; max-width: 400px; text-align: center; }
        .modal h2 { margin: 0 0 16px; font-size: 20px; }
        .modal p { color: #5b6660; margin-bottom: 24px; }
        .modal-buttons { display: flex; gap: 12px; justify-content: center; }
        .btn-cancel { background: #e5e7eb; color: #374151; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; }
        .btn-confirm { background: #16a34a; color: #fff; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Voor Ieder Moment</div>
            <h1>Kies je favoriete nummer</h1>
            <p class="subtitle">Beluister alle 4 de versies en kies degene die je het mooist vindt.</p>
        </div>

        <form id="chooseForm" action="{{ route('samples.choose', ['token' => $token]) }}" method="POST">
            @csrf
            <div class="samples-grid">
                @foreach($samples as $sample)
                    <label class="sample-card" for="sample_{{ $sample->id }}">
                        <input type="radio" name="sample_id" id="sample_{{ $sample->id }}" value="{{ $sample->id }}" required>
                        <img class="cover" src="{{ Storage::disk($sample->storage_disk)->url($sample->cover_path) }}" alt="Cover">
                        <div class="sample-info">
                            <div class="sample-number">Versie {{ $sample->position }}</div>
                            <div class="sample-title">{{ $sample->title }}</div>
                            <audio controls preload="metadata">
                                <source src="{{ Storage::disk($sample->storage_disk)->url($sample->preview_path) }}" type="audio/mpeg">
                            </audio>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="actions">
                <button type="button" id="chooseBtn" class="btn" disabled>Kies dit nummer</button>
            </div>
        </form>

        <p class="expires">
            Deze link is geldig tot {{ $samples->first()->expires_at->format('d-m-Y') }}
        </p>
    </div>

    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <h2>Weet je het zeker?</h2>
            <p>Na je keuze worden de andere versies verwijderd en kun je niet meer wijzigen.</p>
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" id="cancelBtn">Annuleren</button>
                <button type="button" class="btn-confirm" id="confirmBtn">Ja, dit is mijn keuze</button>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('chooseForm');
        const chooseBtn = document.getElementById('chooseBtn');
        const confirmModal = document.getElementById('confirmModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const cards = document.querySelectorAll('.sample-card');

        // Enable button when radio selected
        form.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                chooseBtn.disabled = false;
                cards.forEach(c => c.classList.remove('selected'));
                radio.closest('.sample-card').classList.add('selected');
            });
        });

        // Show modal
        chooseBtn.addEventListener('click', () => {
            confirmModal.classList.add('active');
        });

        // Cancel
        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });

        // Confirm
        confirmBtn.addEventListener('click', () => {
            form.submit();
        });

        // Close modal on backdrop click
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) {
                confirmModal.classList.remove('active');
            }
        });
    </script>
</body>
</html>
