<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Poker | Yeni Oda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 flex items-center justify-center h-screen text-slate-200 selection:bg-violet-500 selection:text-white">
    
    <div class="text-center w-full max-w-md px-8 py-10 bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 shadow-2xl">
        
        <div class="mb-8 inline-flex items-center justify-center w-16 h-16 rounded-full bg-violet-600/20 text-violet-500 mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>

        <h1 class="text-3xl font-semibold mb-2 text-white">Yeni Oda Oluştur</h1>
        <p class="text-slate-400 mb-8 text-sm">Ekibini topla ve tahminlemeye başla.</p>

        <form action="{{ route('teams.store') }}" method="POST">
            @csrf 
            <div class="mb-4 relative">
                <input type="text" name="name" placeholder="Oda Adı" required autocomplete="off"
                       class="w-full text-center border border-slate-700 bg-slate-950/50 rounded-lg py-3 px-4 text-lg text-white placeholder-slate-500 focus:outline-none focus:border-violet-500 transition-all">
            </div>

            <div class="mb-8 relative">
                <input type="text" name="user_name" placeholder="Senin Adın" required autocomplete="off"
                       class="w-full text-center border border-slate-700 bg-slate-950/50 rounded-lg py-3 px-4 text-lg text-white placeholder-slate-500 focus:outline-none focus:border-violet-500 transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-violet-600 text-white font-medium px-8 py-3 rounded-lg shadow-lg shadow-violet-600/20 hover:bg-violet-500 hover:-translate-y-0.5 transition-all duration-200">
                Oluştur ve Katıl
            </button>
        </form>
    </div>

</body>
</html>