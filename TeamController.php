<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index() { return view('welcome'); }

    // Oda Kurma ve Kurucuyu Odaya Ekleme
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:50', // Kurucunun adını da doğruluyoruz
        ]);

        $slug = Str::slug($request->name) . '-' . Str::random(5);
        
        $team = Team::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        // Kurucuyu veritabanına kaydet
        $user = TeamUser::create([
            'team_id' => $team->id,
            'name' => $request->user_name,
        ]);

        // Kurucuyu sisteme tanıt (Oturum açtır)
        session()->put('user_id_' . $team->id, $user->id);

        return redirect()->route('teams.show', $team->slug);
    }

    // Odayı Gösterme
    public function show($slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $allUsers = $team->users; 

        // Kullanıcı bu odaya daha önce girmiş mi kontrol et
        $currentUserId = session('user_id_' . $team->id);
        $currentUser = $currentUserId ? TeamUser::find($currentUserId) : null;

        // Her halükarda poker blade'ine gönder, kimlik kontrolünü blade içinde yapacağız
        return view('poker', compact('team', 'allUsers', 'currentUser'));
    }

    // Dışarıdan Linkle Gelenlerin Odaya Katılması
    public function join(Request $request, $slug)
    {
        $request->validate(['user_name' => 'required|string|max:50']);
        $team = Team::where('slug', $slug)->firstOrFail();

        $user = TeamUser::create([
            'team_id' => $team->id,
            'name' => $request->user_name,
        ]);

        session()->put('user_id_' . $team->id, $user->id);

        return redirect()->route('teams.show', $team->slug);
    }

    public function submitVote(Request $request, $slug)
    {
        // Gelen veriyi doğrula
        $request->validate([
            'vote' => 'required|string|max:5',
        ]);

        $team = Team::where('slug', $slug)->firstOrFail();

        // Oturumdaki kullanıcıyı bul
        $userId = session('user_id_' . $team->id);
        if (!$userId) { return response()->json(['error' => 'Yetkisiz'], 401); }

        $user = TeamUser::find($userId);
        if (!$user) { return response()->json(['error' => 'Kullanıcı bulunamadı'], 404); }

        // Oyunu güncelle
        $user->vote = $request->vote;
        $user->save();

        return response()->json(['success' => true, 'vote' => $user->vote]);
    } 

    public function revealVotes($slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $team->is_revealed = true;
        $team->save();

        return response()->json(['success' => true]);
    }

    // 6. Yeni Tur Başlat (Reset)
    public function resetVotes($slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        
        // Odayı tekrar gizli hale getir
        $team->is_revealed = false;
        $team->save();

        // Odadaki herkesin oyunu sıfırla (null yap)
        TeamUser::where('team_id', $team->id)->update(['vote' => null]);

        return response()->json(['success' => true]);
    }
}