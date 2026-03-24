<script setup lang="ts">
useSeoMeta({
  title: 'Aanvraag',
  description: 'Stel jouw gepersonaliseerde nummer samen en start direct de aanvraag.',
});

const selectedGenres = ref(['Pop', 'Jazz', 'Blues']);
const packageType = ref('premium');
const vocals = ref('man');
const lyricStyle = ref('grappig');

function toggleGenre(genre: string) {
  if (selectedGenres.value.includes(genre)) {
    selectedGenres.value = selectedGenres.value.filter((item) => item !== genre);
    return;
  }
  selectedGenres.value = [...selectedGenres.value, genre];
}

async function handleSubmit() {
  await navigateTo('/checkout');
}
</script>

<template>
  <div class="px-5 py-14 sm:px-8 sm:py-16">
    <div class="mx-auto max-w-6xl">
      <header class="mb-14 text-center">
        <span class="section-kicker">Personaliseer jouw verhaal</span>
        <h1 class="section-heading text-5xl md:text-6xl">
          Vraag Jouw Unieke
          <span class="block italic text-[var(--color-primary)]">Compositie Aan</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-[var(--color-on-surface-muted)]">
          Wij vertalen uw kostbare herinneringen naar muziek. Vul het onderstaande formulier in en begin de reis.
        </p>
      </header>

      <form class="space-y-6" @submit.prevent="handleSubmit">
        <section class="grid gap-6 lg:grid-cols-[1.5fr_0.75fr]">
          <div class="rich-card p-6 sm:p-8">
            <h2 class="mb-6 text-lg font-bold text-white">Voor wie is het lied?</h2>
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Ontvanger</label>
                <select class="w-full rounded-2xl border-none bg-[#0f0f0f] px-4 py-3.5 text-sm text-white outline-none">
                  <option>Persoon</option>
                  <option>Organisatie</option>
                  <option>Evenement</option>
                  <option>Huisdier</option>
                </select>
              </div>
              <div>
                <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Branche / Categorie</label>
                <input class="w-full rounded-2xl border-none bg-[#0f0f0f] px-4 py-3.5 text-sm text-white outline-none" placeholder="Bijv. Bruiloft, Afscheid, Jubileum" />
              </div>
            </div>
          </div>

          <aside class="rich-card bg-[#2a2a2a] p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white">Onze Pakketten</h2>
            <p class="mt-2 text-xs text-[var(--color-on-surface-muted)]">Selecteer uw gewenste kwaliteit en release optie.</p>
            <div class="mt-6 space-y-4">
              <button type="button" class="flex w-full items-center justify-between rounded-2xl border px-4 py-4 text-left transition duration-200" :class="packageType === 'basic' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)]' : 'border-transparent bg-[#111] hover:border-[rgba(242,202,80,0.14)]'" @click="packageType = 'basic'">
                <span>
                  <span class="block font-semibold text-white">Digitaal Basis</span>
                  <span class="text-xs text-[var(--color-on-surface-muted)]">MP3 via E-mail</span>
                </span>
                <span class="font-display text-3xl text-[var(--color-primary)]">€5,99</span>
              </button>
              <button type="button" class="flex w-full items-center justify-between rounded-2xl border px-4 py-4 text-left transition duration-200" :class="packageType === 'premium' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)]' : 'border-transparent bg-[#111] hover:border-[rgba(242,202,80,0.14)]'" @click="packageType = 'premium'">
                <span>
                  <span class="block font-semibold text-white">Premium Spotify</span>
                  <span class="text-xs text-[var(--color-on-surface-muted)]">Officiële Release</span>
                </span>
                <span class="font-display text-3xl text-[var(--color-primary)]">€9,99</span>
              </button>
            </div>
          </aside>
        </section>

        <section class="rich-card p-6 sm:p-8">
          <div class="mb-6">
            <h2 class="text-lg font-bold text-white">Kies het Genre</h2>
            <p class="mt-2 text-xs text-[var(--color-on-surface-muted)]">Selecteer minimaal 3 genres voor de perfecte blend.</p>
          </div>
          <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <button
              v-for="genre in ['Pop', 'Jazz', 'Klassiek', 'Acoustic', 'Blues', 'Folk']"
              :key="genre"
              type="button"
              class="rounded-full px-4 py-3 text-xs font-bold uppercase tracking-[0.12em] transition"
              :class="selectedGenres.includes(genre) ? 'bg-[var(--color-primary-deep)] text-[var(--color-on-primary)] shadow-[0_12px_22px_rgba(212,175,55,0.14)]' : 'bg-[#0f0f0f] text-white hover:bg-[#161616]'"
              @click="toggleGenre(genre)"
            >
              {{ genre }}
            </button>
          </div>
        </section>

        <section class="grid gap-6 md:grid-cols-2">
          <div class="rich-card p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white">Taal & Stem</h2>
            <div class="mt-6">
              <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Voorkeur taal</label>
              <input value="Nederlands" class="w-full rounded-2xl border-none bg-[#0f0f0f] px-4 py-3.5 text-sm text-white outline-none" />
            </div>
            <div class="mt-6">
              <label class="mb-3 block text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Vocalen</label>
              <div class="grid gap-3 sm:grid-cols-2">
                <button type="button" class="rounded-full border px-4 py-3 text-sm transition" :class="vocals === 'man' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)] text-white' : 'border-[rgba(77,70,53,0.3)] text-[var(--color-on-surface-muted)] hover:bg-[#232221]'" @click="vocals = 'man'">Man</button>
                <button type="button" class="rounded-full border px-4 py-3 text-sm transition" :class="vocals === 'vrouw' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)] text-white' : 'border-[rgba(77,70,53,0.3)] text-[var(--color-on-surface-muted)] hover:bg-[#232221]'" @click="vocals = 'vrouw'">Vrouw</button>
              </div>
            </div>
          </div>

          <div class="rich-card p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white">Stijl van Tekst</h2>
            <div class="mt-6 space-y-3">
              <button type="button" class="flex w-full items-center rounded-2xl border px-4 py-3.5 text-left text-sm transition" :class="lyricStyle === 'grappig' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)] text-white' : 'border-[rgba(77,70,53,0.3)] text-[var(--color-on-surface-muted)] hover:bg-[#232221]'" @click="lyricStyle = 'grappig'">Grappig & Lichtvoetig</button>
              <button type="button" class="flex w-full items-center rounded-2xl border px-4 py-3.5 text-left text-sm transition" :class="lyricStyle === 'serieus' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)] text-white' : 'border-[rgba(77,70,53,0.3)] text-[var(--color-on-surface-muted)] hover:bg-[#232221]'" @click="lyricStyle = 'serieus'">Serieus & Emotioneel</button>
              <button type="button" class="flex w-full items-center rounded-2xl border px-4 py-3.5 text-left text-sm transition" :class="lyricStyle === 'romantisch' ? 'border-[var(--color-primary)] bg-[rgba(242,202,80,0.08)] text-white' : 'border-[rgba(77,70,53,0.3)] text-[var(--color-on-surface-muted)] hover:bg-[#232221]'" @click="lyricStyle = 'romantisch'">Lief & Romantisch</button>
            </div>
          </div>
        </section>

        <section class="rich-card bg-[#2a2a2a] p-6 sm:p-8">
          <h2 class="text-lg font-bold text-white">De Kern van het Verhaal</h2>
          <div class="mt-6 grid gap-4 md:grid-cols-3">
            <input class="rounded-2xl border-none bg-[#111] px-4 py-3.5 text-sm text-white outline-none" placeholder="1. De eerste ontmoeting" />
            <input class="rounded-2xl border-none bg-[#111] px-4 py-3.5 text-sm text-white outline-none" placeholder="2. Een gedeelde hobby" />
            <input class="rounded-2xl border-none bg-[#111] px-4 py-3.5 text-sm text-white outline-none" placeholder="3. Dat ene vakantiemoment" />
          </div>
          <div class="mt-5">
            <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--color-on-surface-muted)]">Grappige feiten of inside jokes</label>
            <textarea rows="5" class="w-full rounded-[1.5rem] border-none bg-[#111] px-4 py-3.5 text-sm text-white outline-none" placeholder="Vertel ons iets unieks wat alleen jullie begrijpen..." />
          </div>
        </section>

        <footer class="panel flex flex-col gap-5 p-6 sm:p-8 md:flex-row md:items-center md:justify-between">
          <p class="max-w-xl text-sm leading-6 text-[var(--color-on-surface-muted)]">
            Uw gegevens worden veilig verwerkt en alleen gebruikt voor het componeren van uw unieke nummer.
          </p>
          <div class="flex flex-col gap-3 sm:flex-row">
            <button type="button" class="stitch-outline-button text-xs uppercase tracking-[0.16em]">
              Opslaan als concept
            </button>
            <button type="submit" class="stitch-button px-8 py-3.5">Aanvraag Voltooien →</button>
          </div>
        </footer>

        <section class="overflow-hidden rounded-[1.5rem] shadow-[0_22px_60px_rgba(0,0,0,0.25)]">
          <div class="relative aspect-[21/9]">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBKge8EWu4KUUqAlq7Qihy4im49mDFagUxInEDAKR7EzTelQvVRplZYKrjfpgtjD7u1U0-GM5EbKkxCT9Snasbm-6wNasof3fH0P7puUjqtghszeB2AyNDfxlh_9HHQVPZ6Jk3k5Kj29rHriOrdRawIhRYk9FLBdhviI1v9U_GyUl5MutrDGIM3li4izSOw9Rbjg3ZwptwSWQS44baUFBgwalmupoZrOC8Ol1-qHusupwZhyLyvr90i2x3PnGsUoipdR0uNJ_Qn28"
              alt=""
              class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-[linear-gradient(180deg,transparent,rgba(19,19,19,0.82),#131313)] p-8 sm:p-12">
              <div class="flex h-full items-end">
                <div>
                  <p class="max-w-xl font-display text-2xl italic text-white">
                    "Muziek geeft een ziel aan het universum, vleugels aan de geest en een vlucht aan de verbeelding."
                  </p>
                  <p class="mt-4 text-xs font-bold uppercase tracking-[0.18em] text-[var(--color-primary)]">— Plato</p>
                </div>
              </div>
            </div>
          </div>
        </section>
      </form>
    </div>
  </div>
</template>
