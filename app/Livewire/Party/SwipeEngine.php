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
    public ?array $lastMatch = null;

    private Collection $queue;

    public function mount(Party $party): void
    {
        $this->party = $party;
        $this->loadQueue();
        $this->advanceQueue();
    }

    /**
     * Carga la cola de usuarios filtrando por preferencia sexual.
     */
    private function loadQueue(): void
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

        $this->queue = $query->inRandomOrder()->get(['id', 'username', 'name', 'age', 'bio', 'profile_photo_path', 'gender_identity']);
    }

    /**
     * Aplica filtros de preferencia sexual con lógica de compatibilidad mutua.
     *
     * Lógica:
     *  - hetero (hombre): ve mujeres con hetero o bi
     *  - hetero (mujer):  ve hombres con hetero o bi
     *  - homo   (hombre): ve hombres con homo o bi
     *  - homo   (mujer):  ve mujeres con homo o bi
     *  - bi     (hombre): ve hombres con homo/bi + mujeres con hetero/bi
     *  - bi     (mujer):  ve mujeres con homo/bi + hombres con hetero/bi
     */
    private function applyPreferenceFilter($query, User $me)
    {
        $myPref   = $me->sexual_preference ?? 'bi';
        $myGender = $me->gender_identity   ?? 'man';

        // Género opuesto
        $opposite = $myGender === 'man' ? 'woman' : 'man';

        return $query->where(function ($q) use ($myPref, $myGender, $opposite) {

            if ($myPref === 'hetero') {
                // Me interesan el género opuesto que también se interese en mí
                // Ellos deben ser del género opuesto Y tener hetero o bi
                $q->where('gender_identity', $opposite)
                  ->whereIn('sexual_preference', ['hetero', 'bi']);

            } elseif ($myPref === 'homo') {
                // Me interesa mi mismo género que también se interese en mí
                // Ellos deben ser de mi género Y tener homo o bi
                $q->where('gender_identity', $myGender)
                  ->whereIn('sexual_preference', ['homo', 'bi']);

            } elseif ($myPref === 'bi') {
                // Me interesan ambos géneros
                // Hombres que les gustan personas de mi género
                // + Mujeres que les gustan personas de mi género
                $q->where(function ($sub) use ($myGender, $opposite) {
                    // Mi mismo género con homo o bi
                    $sub->where('gender_identity', $myGender)
                        ->whereIn('sexual_preference', ['homo', 'bi']);
                })->orWhere(function ($sub) use ($myGender, $opposite) {
                    // Género opuesto con hetero o bi
                    $sub->where('gender_identity', $opposite)
                        ->whereIn('sexual_preference', ['hetero', 'bi']);
                });
            }
        });
    }

    /**
     * Avanza la cola: current ← next ← queue.shift()
     */
    private function advanceQueue(): void
    {
        $this->currentCard = $this->nextCard;
        $next = $this->queue->shift();

        if ($next) {
            $this->nextCard = $this->userToCard($next);
        } else {
            $this->nextCard = null;
        }

        if ($this->currentCard === null && $this->nextCard !== null) {
            $this->currentCard = $this->nextCard;
            $next2 = $this->queue->shift();
            $this->nextCard = $next2 ? $this->userToCard($next2) : null;
        }

        $this->noMoreCards = ($this->currentCard === null);
    }

    /**
     * Convierte un modelo User en array para la tarjeta.
     */
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

    /**
     * Registra un swipe y comprueba si hay match.
     */
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
                [$u1, $u2] = $me->id < $swipedId ? [$me->id, $swipedId] : [$swipedId, $me->id];

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

    /**
     * Recarga la cola (botón "Actualizar" cuando no hay más perfiles).
     */
    public function reload(): void
    {
        $this->loadQueue();
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
}