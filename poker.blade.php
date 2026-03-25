<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} | Planning Poker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen text-slate-200 selection:bg-violet-500">

    <header class="border-b border-slate-800 bg-slate-900/50 py-4 px-8 flex justify-between items-center">
        <h1 class="text-2xl font-semibold flex items-center gap-3 text-white">
            <span class="text-violet-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 4 0 00-5.656 0l-4 4a4 4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 4 0 005.656 0l4-4a4 4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            </span> 
            {{ $team->name }}
        </h1>
        <a href="{{ route('home') }}" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">
            Yeni Oda
        </a>
    </header>

    <main class="max-w-5xl mx-auto mt-16 px-6">

        @if(!$currentUser)
            <div class="max-w-md mx-auto mt-20 text-center bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 p-8 shadow-2xl">
                <h2 class="text-2xl font-semibold mb-2 text-white">Odaya Katıl</h2>
                <p class="text-slate-400 mb-8 text-sm">Oylamaya katılmak için ismini gir.</p>

                <form action="{{ route('teams.join', $team->slug) }}" method="POST">
                    @csrf 
                    <div class="mb-6">
                        <input type="text" name="user_name" placeholder="Adın nedir?" required autocomplete="off"
                               class="w-full text-center border border-slate-700 bg-slate-950/50 rounded-lg py-3 px-4 text-lg text-white focus:outline-none focus:border-violet-500 transition-all">
                    </div>
                    <button type="submit" class="w-full bg-violet-600 text-white font-medium px-8 py-3 rounded-lg hover:bg-violet-500 transition-all">
                        Masaya Otur
                    </button>
                </form>
            </div>
        @else
            <div class="mb-20">
                <h2 class="text-center text-slate-400 mb-8 text-sm uppercase tracking-widest">Tahminini Seç, {{ $currentUser->name }}</h2>
                
                <div id="poker-cards" class="flex flex-wrap justify-center gap-4">
                    @php $cards = ['1', '2', '3', '5', '8', '13', '21', '?', '☕']; @endphp

                    @foreach($cards as $card)
                    <button 
                        class="poker-card w-16 h-24 md:w-20 md:h-28 border border-slate-700 rounded-xl flex items-center justify-center text-2xl md:text-3xl font-semibold shadow-lg hover:-translate-y-2 hover:border-violet-500 transition-all duration-200 
                               {{ $currentUser->vote === $card ? 'bg-violet-600 text-white' : 'bg-slate-800 text-slate-300' }}"
                        data-vote="{{ $card }}"
                        @if($team->is_revealed) disabled @endif>
                        {{ $card }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-800 pt-12">
                @foreach($allUsers as $user)
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                    <div class="text-lg font-medium text-white flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-slate-800 flex items-center justify-center text-sm">👤</div>
                        {{ $user->name }} 
                        @if($user->id === $currentUser->id) <span class="text-xs text-slate-500 font-normal">(Sen)</span> @endif
                    </div>
                    
                    <div class="w-10 h-14 bg-slate-800 rounded border border-slate-700 flex items-center justify-center text-violet-400 font-bold text-xl">
                        @if($team->is_revealed)
                            <span class="text-white">{{ $user->vote ?? '-' }}</span>
                        @else
                            @if($user->vote) ✓ @else ... @endif
                        @endif
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-12">
                    @if(!$team->is_revealed)
                        <button id="btn-reveal" class="bg-slate-800 text-slate-300 hover:text-white border border-slate-700 px-10 py-3 rounded-lg font-medium tracking-wide hover:bg-violet-600 hover:border-violet-500 transition-all">
                            Oyları Aç (Show)
                        </button>
                    @else
                        <button id="btn-reset" class="bg-violet-600 text-white px-10 py-3 rounded-lg font-medium tracking-wide shadow-lg hover:bg-violet-500 transition-all">
                            Yeni Tur Başlat
                        </button>
                    @endif
                </div>
            </div>
        @endif

    </main>

    @if($currentUser)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. KARTLARA TIKLAYIP OY VERME İŞLEMİ
            const cards = document.querySelectorAll('.poker-card');
            const voteUrl = "{{ route('teams.vote', $team->slug) }}";
            const isRevealed = {{ $team->is_revealed ? 'true' : 'false' }};

            cards.forEach(card => {
                card.addEventListener('click', function () {
                    if(isRevealed) return; // Oylar açıldıysa oy değiştirmeyi engelle

                    const selectedVote = this.getAttribute('data-vote');
                    
                    // Görseli güncelle
                    cards.forEach(c => {
                        c.classList.remove('bg-violet-600', 'text-white');
                        c.classList.add('bg-slate-800', 'text-slate-300');
                    });
                    this.classList.remove('bg-slate-800', 'text-slate-300');
                    this.classList.add('bg-violet-600', 'text-white');

                    // Veritabanına gönder
                    fetch(voteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ vote: selectedVote })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Şimdilik sayfayı yeniliyoruz
                        }
                    });
                });
            });

            // 2. OYLARI AÇMA (SHOW) İŞLEMİ
            const btnReveal = document.getElementById('btn-reveal');
            if (btnReveal) {
                btnReveal.addEventListener('click', function () {
                    fetch("{{ route('teams.reveal', $team->slug) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if(data.success) location.reload();
                    });
                });
            }

            // 3. YENİ TUR (RESET) İŞLEMİ
            const btnReset = document.getElementById('btn-reset');
            if (btnReset) {
                btnReset.addEventListener('click', function () {
                    fetch("{{ route('teams.reset', $team->slug) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if(data.success) location.reload();
                    });
                });
            }

        });
    </script>
    @endif

</body>
</html>