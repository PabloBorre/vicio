<?php

namespace App\Livewire\Party;

use App\Models\Party;
use App\Models\PartyMatch;
use App\Models\Swipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class SwipeEngine extends Component
{
    public Party $party;
    public ?array $currentCard  = null;
    public ?array $nextCard     = null;
    public bool   $noMoreCards  = false;
    public ?array $lastMatch    = null;

    /** IDs pendientes — público para que Livewire lo serialice entre requests */
    public array $pendingIds = [];

    public function mount(Party $party): void
    {
        $this->party = $party;
        $this->loadPendingIds();
        $this->advanceQueue();
    }

    /**
     * Carga los IDs de la cola filtrando por preferencia.
     */
    private function loadPendingIds(): void
    {
        $me = auth()->user();

        $swipedIds = Swipe::where('swiper_id', $me->id)
            ->where('party_id', $this->party->id)
            ->pluck('swiped_id');

        $query = User::whereHas('parties', fn($q) => $q->where('party_id', $this->party->id))
            ->where('id', '!=', $me->id)
            ->whereNotIn('id', $swipedIds)
            ->where('is_banned', false);

        $query = $this->applyPreferenceFilter($query, $me);

        $this->pendingIds = $query->inRandomOrder()->pluck('id')->toArray();
    }

    /**
     * Devuelve la cola como Collection a partir de $pendingIds.
     */
    private function getQueue(): Collection
    {
        if (empty($this->pendingIds)) {
            return collect();
        }

        return User::whereIn('id', $this->pendingIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->pendingIds) . ')')
            ->get(['id', 'username', 'name', 'age', 'bio', 'profile_photo_path', 'gender_identity']);
    }

    private function applyPreferenceFilter($query, User $me)
{
    $myPref   = $me->sexual_preference ?? 'both';
    $myGender = $me->gender_identity   ?? 'man';
    $opposite = $myGender === 'man' ? 'woman' : 'man';

    return $query->where(function ($q) use ($myPref, $myGender, $opposite) {
        if ($myPref === 'man') {
            $q->where('gender_identity', 'man')
              ->whereIn('sexual_preference', ['man', 'both']);
        } elseif ($myPref === 'woman') {
            $q->where('gender_identity', 'woman')
              ->whereIn('sexual_preference', ['woman', 'both']);
        } elseif ($myPref === 'both') {
            $q->where(function ($sub) use ($myGender) {
                $sub->where('gender_identity', $myGender)
                    ->whereIn('sexual_preference', [$myGender, 'both']);
            })->orWhere(function ($sub) use ($opposite) {
                $sub->where('gender_identity', $opposite)
                    ->whereIn('sexual_preference', [$opposite, 'both']);
            });
        }
    });
}

    /**
     * Avanza la cola: current ← next ← pendingIds.shift()
     */
    private function advanceQueue(): void
    {
        $this->currentCard = $this->nextCard;

        $nextId = array_shift($this->pendingIds);

        if ($nextId) {
            $user = User::find($nextId, ['id', 'username', 'name', 'age', 'bio', 'profile_photo_path', 'gender_identity']);
            $this->nextCard = $user ? $this->userToCard($user) : null;
        } else {
            $this->nextCard = null;
        }

        // Primera carga: currentCard estaba null, promover nextCard
        if ($this->currentCard === null && $this->nextCard !== null) {
            $this->currentCard = $this->nextCard;
            $nextId2 = array_shift($this->pendingIds);
            if ($nextId2) {
                $user2 = User::find($nextId2, ['id', 'username', 'name', 'age', 'bio', 'profile_photo_path', 'gender_identity']);
                $this->nextCard = $user2 ? $this->userToCard($user2) : null;
            } else {
                $this->nextCard = null;
            }
        }

        $this->noMoreCards = ($this->currentCard === null);
    }

    private function userToCard(User $user): array
    {
        return [
            'id'                => $user->id,
            'username'          => $user->username ?? $user->name,
            'age'               => $user->age,
            'bio'               => $user->bio,
            'profile_photo_url' => $user->profile_photo_url,
            'gender_identity'   => $user->gender_identity,
        ];
    }

    public function swipe(string $direction): void
    {
        if (!$this->currentCard) return;

        $me       = auth()->user();
        $swipedId = $this->currentCard['id'];

        Swipe::firstOrCreate([
            'swiper_id' => $me->id,
            'swiped_id' => $swipedId,
            'party_id'  => $this->party->id,
        ], ['direction' => $direction]);

        if ($direction === 'like') {
            $mutualLike = Swipe::where('swiper_id', $swipedId)
                ->where('swiped_id', $me->id)
                ->where('party_id', $this->party->id)
                ->where('direction', 'like')
                ->exists();

            if ($mutualLike) {
                [$u1, $u2] = $me->id < $swipedId
                    ? [$me->id, $swipedId]
                    : [$swipedId, $me->id];

                PartyMatch::firstOrCreate([
                    'user1_id' => $u1,
                    'user2_id' => $u2,
                    'party_id' => $this->party->id,
                ]);

                $matchedUser = User::find($swipedId);
                $this->lastMatch = [
                    'username'          => $matchedUser->username ?? $matchedUser->name,
                    'profile_photo_url' => $matchedUser->profile_photo_url,
                ];
            }
        }

        $this->advanceQueue();
    }

    public function reload(): void
    {
        $this->loadPendingIds();
        $this->currentCard = null;
        $this->nextCard    = null;
        $this->advanceQueue();
    }

    public function dismissMatch(): void
    {
        $this->lastMatch = null;
    }

    public function render()
    {
        return view('livewire.party.swipe-engine');
    }

    public function checkIfFinished(): void
    {
        $this->party->refresh();
        if ($this->party->status === 'finished') {
            $this->redirect(route('party.finished', $this->party->qr_code), navigate: true);
        }
    }
}   