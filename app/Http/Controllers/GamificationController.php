<?php

namespace App\Http\Controllers;

use App\Models\GamificationBadge;
use App\Models\GamificationBadgeAssignment;
use App\Models\GamificationChallenge;
use App\Models\GamificationChallengeLine;
use App\Models\GamificationGoal;
use App\Models\GamificationGoalDefinition;
use App\Models\GamificationKarmaRank;
use App\Models\GamificationKarmaTracking;
use App\Models\User;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public function index()
    {
        $this->authorize('gamification.read');
        $stats = [
            'total_badges' => GamificationBadge::count(),
            'total_challenges' => GamificationChallenge::count(),
            'active_challenges' => GamificationChallenge::where('state', 'in_progress')->count(),
            'total_assignments' => GamificationBadgeAssignment::count(),
            'users_with_karma' => GamificationKarmaTracking::distinct('user_id')->count('user_id'),
        ];

        return view('erp.gamification.index', compact('stats'));
    }

    public function badges()
    {
        $this->authorize('gamification.read');
        $badges = GamificationBadge::withCount('assignments')->get();

        return view('erp.gamification.badges', compact('badges'));
    }

    public function challenges()
    {
        $this->authorize('gamification.read');
        $challenges = GamificationChallenge::with(['lines', 'rewardBadge'])->latest()->get();

        return view('erp.gamification.challenges', compact('challenges'));
    }

    public function leaderboard()
    {
        $this->authorize('gamification.read');
        $users = User::withCount(['badgeAssignments as karma' => function ($q) {
            $q->selectRaw('COALESCE(SUM(level * 10), 0)');
        }])->orderByDesc('karma')->get();
        $ranks = GamificationKarmaRank::orderByDesc('karma_min')->get();

        return view('erp.gamification.leaderboard', compact('users', 'ranks'));
    }

    public function storeBadge(Request $request)
    {
        $this->authorize('gamification.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|integer|min:1',
        ]);

        GamificationBadge::create($validated);

        return back()->with('success', 'Badge created successfully.');
    }

    public function storeChallenge(Request $request)
    {
        $this->authorize('gamification.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reward_badge_id' => 'nullable|exists:gamification_badges,id',
        ]);

        GamificationChallenge::create($validated);

        return back()->with('success', 'Challenge created successfully.');
    }

    public function assignBadge(Request $request, GamificationBadge $badge)
    {
        $this->authorize('gamification.update');
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comment' => 'nullable|string',
        ]);

        GamificationBadgeAssignment::create([
            'badge_id' => $badge->id,
            'user_id' => $validated['user_id'],
            'sender_id' => auth()->id(),
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Badge assigned successfully.');
    }

    public function startChallenge(Request $request, GamificationChallenge $challenge)
    {
        $this->authorize('gamification.update');
        $challenge->update(['state' => 'in_progress']);

        return back()->with('success', 'Challenge started successfully.');
    }
}
