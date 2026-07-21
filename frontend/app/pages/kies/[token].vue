<script setup lang="ts">
const route = useRoute();
const token = route.params.token as string;

const { data, error, pending } = await useFetch(`/api/v1/select/${token}`, {
  baseURL: useRuntimeConfig().public.apiBaseUrl.replace('/api/v1', ''),
});

const selectedSample = ref<number | null>(null);
const submitting = ref(false);
const submitted = ref(false);
const submitError = ref('');

interface Sample {
  id: number;
  url: string;
  title?: string;
  duration?: number;
}

const samples = computed<Sample[]>(() => (data.value as any)?.samples || []);
const recipientName = computed(() => (data.value as any)?.recipient_name || '');
const categoryTitle = computed(() => (data.value as any)?.category_title || '');
const alreadyChosen = computed(() => (data.value as any)?.already_chosen || false);
const chosenSampleId = computed(() => (data.value as any)?.chosen_sample_id || null);

async function submitChoice() {
  if (!selectedSample.value) return;

  submitting.value = true;
  submitError.value = '';

  try {
    const response = await $fetch(`/api/v1/select/${token}`, {
      baseURL: useRuntimeConfig().public.apiBaseUrl.replace('/api/v1', ''),
      method: 'POST',
      body: { sample_id: selectedSample.value },
    });

    submitted.value = true;
  } catch (e: any) {
    submitError.value = e?.data?.error || 'Er ging iets mis. Probeer het opnieuw.';
  } finally {
    submitting.value = false;
  }
}

useSeoMeta({
  title: 'Kies je favoriete sample',
  robots: 'noindex',
});
</script>

<template>
  <div class="min-h-screen py-16 sm:py-24">
    <div class="site-container">
      <div class="mx-auto max-w-3xl">
        <!-- Loading -->
        <div v-if="pending" class="text-center">
          <div class="mx-auto h-12 w-12 animate-spin rounded-full border-4 border-[var(--accent-soft)] border-t-[var(--accent)]" />
          <p class="mt-4 text-[var(--color-ink-soft)]">Laden...</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="rich-card p-10 text-center">
          <div class="bento-card-icon mx-auto mb-6 text-3xl">😕</div>
          <h1 class="section-heading text-2xl sm:text-3xl">Oeps!</h1>
          <p class="mt-4 text-[var(--color-ink-soft)]">
            {{ (error as any)?.data?.error || 'Deze link is ongeldig of verlopen.' }}
          </p>
          <NuxtLink to="/" class="stitch-button mt-8">
            Naar homepage
          </NuxtLink>
        </div>

        <!-- Already chosen -->
        <div v-else-if="alreadyChosen" class="rich-card p-10 text-center">
          <div class="bento-card-icon mx-auto mb-6 text-3xl">✓</div>
          <h1 class="section-heading text-2xl sm:text-3xl">Je hebt al gekozen!</h1>
          <p class="mt-4 text-[var(--color-ink-soft)]">
            Sample {{ chosenSampleId }} is jouw favoriet. We maken die versie nu compleet.
            Binnen 24–72 uur staat je nummer op Spotify en Apple Music.
          </p>
          <NuxtLink to="/" class="stitch-button mt-8">
            Naar homepage
          </NuxtLink>
        </div>

        <!-- Submitted -->
        <div v-else-if="submitted" class="text-center">
          <div
            v-hero-reveal
            class="mx-auto flex h-20 w-20 items-center justify-center rounded-full text-4xl"
            :style="{ background: 'var(--accent-soft)', color: 'var(--accent-strong)' }"
          >
            <span class="animate-bounce">✓</span>
          </div>
          <h1 v-hero-reveal data-hero-delay="0.1" class="section-heading mt-8 text-3xl sm:text-4xl">
            Bedankt voor je keuze!
          </h1>
          <p v-hero-reveal data-hero-delay="0.2" class="mt-6 text-lg text-[var(--color-ink-soft)]">
            Goede keuze! We maken sample {{ selectedSample }} nu compleet. Binnen 24–72 uur
            staat je nummer op Spotify en Apple Music. Je ontvangt bericht zodra het live is.
          </p>
          <NuxtLink v-hero-reveal data-hero-delay="0.3" to="/" class="stitch-button mt-10">
            Naar homepage
          </NuxtLink>
        </div>

        <!-- Sample selection -->
        <div v-else>
          <div v-reveal class="mb-10 text-center">
            <span class="section-kicker">Jouw samples zijn klaar</span>
            <h1 class="section-heading text-3xl sm:text-4xl lg:text-5xl">
              Kies je favoriet
            </h1>
            <p class="mt-4 text-lg text-[var(--color-ink-soft)]">
              Hieronder staan de 4 samples voor
              <strong :style="{ color: 'var(--accent-strong)' }">{{ recipientName }}</strong>
              ({{ categoryTitle }}). Luister rustig naar alle vier en kies de versie die het beste voelt.
            </p>
          </div>

          <div v-reveal-stagger data-stagger="0.1" class="grid gap-5 sm:grid-cols-2">
            <button
              v-for="sample in samples"
              :key="sample.id"
              type="button"
              class="rich-card p-6 text-left transition-all duration-300"
              :class="{
                'ring-2 ring-[var(--accent)] ring-offset-2': selectedSample === sample.id,
              }"
              @click="selectedSample = sample.id"
            >
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="text-xs font-bold uppercase tracking-[0.16em]" :style="{ color: 'var(--accent-strong)' }">
                    Sample {{ sample.id }}
                  </p>
                  <p class="mt-2 font-display text-xl font-semibold" :style="{ color: 'var(--color-ink)' }">
                    {{ sample.title || `Versie ${sample.id}` }}
                  </p>
                </div>
                <div
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 transition-all"
                  :class="selectedSample === sample.id
                    ? 'border-[var(--accent)] bg-[var(--accent)] text-white'
                    : 'border-[var(--color-line-strong)]'"
                >
                  <span v-if="selectedSample === sample.id">✓</span>
                </div>
              </div>

              <audio
                :src="sample.url"
                controls
                class="mt-4 w-full"
                @click.stop
              />
            </button>
          </div>

          <div v-reveal class="mt-10 text-center">
            <p v-if="submitError" class="mb-4 text-sm text-red-600">
              {{ submitError }}
            </p>

            <button
              class="stitch-button px-12 py-5 text-base"
              :disabled="!selectedSample || submitting"
              :class="{ 'opacity-50 cursor-not-allowed': !selectedSample }"
              @click="submitChoice"
            >
              <span v-if="submitting" class="flex items-center gap-3">
                <span class="h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white" />
                Bezig...
              </span>
              <span v-else-if="selectedSample">
                Bevestig keuze: Sample {{ selectedSample }}
              </span>
              <span v-else>
                Selecteer eerst een sample
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
