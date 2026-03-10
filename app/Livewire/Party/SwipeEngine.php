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
    public ?array $currentCard = null;
    public ?array $nextCard = null;
    public bool $noMoreCards = false;
    public ?array $lastMatch = null; // Para mostrar modal de match

    private Collection $queue;

    public function mount(Party $party): void
    {
        $this->party = $party;
        $this->loadQueue();
        $this->advanceQueue();
    }

    /**
     * Carga la cola de usuarios a mostrar, filtrando por preferencia sexual.
     */
    private function loadQueue(): void
    {
        $me = auth()->user();

        // IDs ya swipeados por mí en esta fiesta
        $swipedIds = Swipe::where('swiper_id', $me->id)
            ->where('party_id', $this->party->id)
            ->pluck('swiped_id');

        // Construir query base: miembros de la fiesta excepto yo y ya swipeados
        $query = User::whereHas('parties', fn($q) => $q->where('party_id', $this->party->id))
            ->where('id', '!=', $me->id)
            ->whereNotIn('id', $swipedIds)
            ->where('is_banned', false);

        // Filtro por preferencia sexual
        $query = $this->applyPreferenceFilter($query, $me);

        $this->queue = $query->inRandomOrder()->get(['id', 'username', 'name', 'age', 'profile_photo_path', 'gender_identity']);
    }

    /**
     * Aplica filtros de preferencia sexual mutuos.
     */
    private function applyPreferenceFilter($query, User $me)
    {
        // Mapa: qué géneros le interesan a cada preferencia
        $genderMap = [
            'hetero' => ['man' => ['woman'], 'woman' => ['man'], 'non_binary' => ['man', 'woman', 'non_binary', 'other'], 'other' => ['man', 'woman', 'non_binary', 'other']],
            'homo'   => ['man' => ['man'], 'woman' => ['woman'], 'non_binary' => ['non_binary'], 'other' => ['other']],
            'bi'     => ['man' => ['man', 'woman'], 'woman' => ['man', 'woman'], 'non_binary' => ['man', 'woman', 'non_binary', 'other'], 'other' => ['man', 'woman', 'non_binary', 'other']],
            'pan'    => ['man' => ['man', 'woman', 'non_binary', 'other'], 'woman' => ['man', 'woman', 'non_binary', 'other'], 'non_binary' => ['man', 'woman', 'non_binary', 'other'], 'other' => ['man', 'woman', 'non_binary', 'other']],
        ];

        $myPref   = $me->sexual_preference ?? 'pan';
        $myGender = $me->gender_identity   ?? 'other';

        // Géneros que me interesan
        $interestedIn = $genderMap[$myPref][$myGender] ?? ['man', 'woman', 'non_binary', 'other'];

        // Solo mostrar personas cuyo género me interesa Y que podrían interesarse en mí
        return $query->whereIn('gender_identity', $interestedIn);
    }

    /**
     * Avanza la cola: current ← next ← queue.shift()
     */
    private function advanceQueue(): void
    {
        $this->currentCard = $this->nextCard;
        $next = $this->queue->shift();

        if ($next) {
            $this->nextCard = [
                'id'                 => $next->id,
                'username'           => $next->username ?? $next->name,
                'age'                => $next->age,
                'profile_photo_url'  => $next->profile_photo_url,
                'gender_identity'    => $next->gender_identity,
            ];
        } else {
            $this->nextCard = null;
        }

        // Si currentCard sigue nulo, intentar de nuevo
        if ($this->currentCard === null && $this->nextCard !== null) {
            $this->currentCard = $this->nextCard;
            $next2 = $this->queue->shift();
            $this->nextCard = $next2 ? [
                'id'                => $next2->id,
                'username'          => $next2->username ?? $next2->name,
                'age'               => $next2->age,
                'profile_photo_url' => $next2->profile_photo_url,
                'gender_identity'   => $next2->gender_identity,
            ] : null;
        }

        $this->noMoreCards = ($this->currentCard === null);
    }

    /**
     * Registra un swipe y comprueba si hay match.
     */
    public function swipe(string $direction): void
    {
        if (!$this->currentCard) return;

        $me      = auth()->user();
        $swipedId = $this->currentCard['id'];

        // Guardar el swipe
        Swipe::firstOrCreate([
            'swiper_id' => $me->id,
            'swiped_id' => $swipedId,
            'party_id'  => $this->party->id,
        ], ['direction' => $direction]);

        // Comprobar match si fue like
        if ($direction === 'like') {
            $mutualLike = Swipe::where('swiper_id', $swipedId)
                ->where('swiped_id', $me->id)
                ->where('party_id', $this->party->id)
                ->where('direction', 'like')
                ->exists();

            if ($mutualLike) {
                // Crear match (user1_id < user2_id para evitar duplicados)
                [$u1, $u2] = $me->id < $swipedId ? [$me->id, $swipedId] : [$swipedId, $me->id];

                PartyMatch::firstOrCreate([
                    'user1_id' => $u1,
                    'user2_id' => $u2,
                    'party_id' => $this->party->id,
                ]);

                // Guardar info del match para mostrar modal
                $matchedUser = User::find($swipedId);
                $this->lastMatch = [
                    'username'          => $matchedUser->username ?? $matchedUser->name,
                    'profile_photo_url' => $matchedUser->profile_photo_url,
                ];

                $this->dispatch('match-found');
            }
        }

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
}