<script setup lang="ts">
import type { Category, IntakeField } from '~/data/categories';
import { themeVars } from '~/data/categories';

const props = defineProps<{ category: Category }>();

const { create } = useSongRequest();
const offer = useOffer();

// Het formulier kleurt mee met de gekozen categorie (ook op /aanvraag, waar
// geen .moment-theme-wrapper omheen staat).
const themeStyle = computed(() => themeVars(props.category.theme));

const values = reactive<Record<string, string>>({});
for (const f of props.category.intakeFields) {
  values[f.name] = '';
}

const errors = reactive<Record<string, boolean>>({});
const started = ref(false);
const currentIndex = ref(0);
const preparing = ref(false);

const nameDetails = reactive({
  recipient: [] as Array<{ name: string; role: string }>,
  from: [] as Array<{ name: string; role: string }>,
});

const itemDetails = reactive({
  anecdotes: [''],
  mustMention: [''],
} as Record<'anecdotes' | 'mustMention', string[]>);

const itemFieldLabels: Record<'anecdotes' | 'mustMention', { singular: string; add: string; placeholder: string }> = {
  anecdotes: {
    singular: 'Situatie',
    add: 'Situatie toevoegen',
    placeholder: 'Beschrijf één concrete situatie, herinnering, grap of anekdote.',
  },
  mustMention: {
    singular: 'Moet erin',
    add: 'Item toevoegen',
    placeholder: 'Eén naam, zin, plek of gebeurtenis die absoluut terug moet komen.',
  },
};

const formSections = [
  { key: 'story', label: 'Verhaal', fields: ['recipientName', 'fromName', 'anecdotes', 'mustMention', 'avoid'] },
  { key: 'music', label: 'Muziek', fields: ['tone', 'vocals', 'musicStyle', 'tempo'] },
  { key: 'details', label: 'Details', fields: [] as string[] },
];

const fields = computed(() => props.category.intakeFields);
const activeField = computed(() => fields.value[currentIndex.value]);
const progress = computed(() => fields.value.length ? Math.round(((currentIndex.value + 1) / fields.value.length) * 100) : 0);
const activeSection = computed(() => sectionForField(activeField.value?.name ?? ''));
const isLastStep = computed(() => currentIndex.value >= fields.value.length - 1);

const loadingWords = ['briefing lezen', 'details wegen', 'rijm zoeken', 'melodie voelen', 'preview klaarzetten'];

function delay(ms: number) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function sectionForField(fieldName: string) {
  return formSections.find((section) => section.fields.includes(fieldName)) ?? formSections[2];
}

function isRepeatableItemField(fieldName: string): fieldName is 'anecdotes' | 'mustMention' {
  return fieldName === 'anecdotes' || fieldName === 'mustMention';
}

function filledItems(fieldName: string) {
  if (!isRepeatableItemField(fieldName)) return [];

  return itemDetails[fieldName]
    .map((item) => item.trim())
    .filter(Boolean);
}

function filledNameRows(rows: Array<{ name: string; role: string }>) {
  return rows
    .map((row) => {
      const name = row.name.trim();
      const role = row.role.trim();
      if (!name && !role) return '';
      return role ? `${name || 'Naam onbekend'} (${role})` : name;
    })
    .filter(Boolean)
    .join(', ');
}

function validateField(f: IntakeField): boolean {
  const repeatableMissing = isRepeatableItemField(f.name) && !filledItems(f.name).length;
  const missing = Boolean(f.required) && (repeatableMissing || (!isRepeatableItemField(f.name) && !values[f.name]?.trim()));
  errors[f.name] = missing;
  return !missing;
}

function validateAll(): boolean {
  return fields.value.every((field) => validateField(field));
}

function questionTitle(f: IntakeField) {
  const titles: Record<string, string> = {
    recipientName: 'Wie staat centraal in het nummer?',
    fromName: 'Van wie komt deze verrassing?',
    tone: 'Welke sfeer moet het nummer krijgen?',
    vocals: 'Welke stem of uitvoering past hierbij?',
    musicStyle: 'Welke muzikale richting wil je op?',
    tempo: 'Hoe snel mag het nummer voelen?',
    anecdotes: 'Welke situaties maken dit verhaal echt persoonlijk?',
    mustMention: 'Wat moet absoluut terugkomen?',
    avoid: 'Wat moeten we juist vermijden?',
    email: 'Waar mogen we de samples naartoe sturen?',
  };

  return titles[f.name] ?? f.label;
}

function questionCopy(f: IntakeField) {
  const copy: Record<string, string> = {
    recipientName: 'Vul één hoofdnaam in. Extra personen, koosnamen of rollen kun je daarna los toevoegen.',
    fromName: 'Vertel wie het nummer geeft. Dat kan één naam zijn, maar ook een groep zoals familie, vrienden of collega’s.',
    tone: 'Kies de emotionele kleur van het nummer. Dit stuurt de tekst, zang en productie.',
    vocals: 'Kies hoe de stem moet aanvoelen. Als je twijfelt, laat ons kiezen wat muzikaal het beste werkt.',
    musicStyle: 'Kies een genre als richting. We gebruiken dit niet star, maar als basis voor de muziekprompt.',
    tempo: 'Kies geen exact BPM-getal, maar hoe snel of rustig het nummer moet voelen.',
    anecdotes: 'Maak per plusje één losse situatie. Bijvoorbeeld één herinnering, één grap, één typische uitspraak of één moment.',
    mustMention: 'Maak per plusje één los verplicht element. Zo kan de tekst per couplet kiezen wat daar natuurlijk past.',
    avoid: 'Noem woorden, grappen, namen of emoties die niet in het nummer mogen belanden.',
    email: 'Hier sturen we de samples en updates naartoe. Gebruik een adres dat je echt checkt.',
  };

  return f.help ?? copy[f.name] ?? 'Vul dit zo concreet mogelijk in. Namen, plekken en echte details maken de songtekst sterker.';
}

function exampleCopy(f: IntakeField) {
  const examples: Record<string, string> = {
    recipientName: 'Bijv. Sophie, papa, oma Els, team JO17-1',
    fromName: 'Bijv. Sanne en Tim, alle kleinkinderen, het bouwteam',
    anecdotes: 'Bijv. “Hij roept altijd: komt goed”, of “die vakantie waar de tent wegwaaide”.',
    mustMention: 'Bijv. “opa’s schuur”, “bouwen op vertrouwen”, “de goal in de laatste minuut”.',
    avoid: 'Bijv. niet te sentimenteel, geen grove grappen, die ene bijnaam liever niet.',
  };

  return examples[f.name] ?? f.placeholder ?? '';
}

function start() {
  started.value = true;
  nextTick(() => focusActiveField());
}

function focusActiveField() {
  const field = activeField.value;
  if (!field || field.type === 'select') return;

  const id = isRepeatableItemField(field.name) ? `input-${field.name}-0` : `input-${field.name}`;
  document.getElementById(id)?.focus();
}

function nextStep() {
  const field = activeField.value;
  if (!field || !validateField(field)) return;

  if (isLastStep.value) {
    onSubmit();
    return;
  }

  currentIndex.value += 1;
  nextTick(() => focusActiveField());
}

function previousStep() {
  if (currentIndex.value <= 0) return;
  currentIndex.value -= 1;
  nextTick(() => focusActiveField());
}

async function onSubmit() {
  if (!validateAll()) {
    const firstError = fields.value.findIndex((field) => errors[field.name]);
    currentIndex.value = Math.max(0, firstError);
    return;
  }

  preparing.value = true;

  const additionalRecipientNames = filledNameRows(nameDetails.recipient);
  const additionalSenderNames = filledNameRows(nameDetails.from);
  const anecdotesItems = filledItems('anecdotes');
  const mustMentionItems = filledItems('mustMention');

  await Promise.all([
    create({
      category: props.category.slug,
      categoryTitle: props.category.title,
      theme: props.category.theme,
      intake: {
        ...values,
        anecdotes: anecdotesItems.join('\n'),
        mustMention: mustMentionItems.join('\n'),
        ...(anecdotesItems.length ? { anecdotesItems } : {}),
        ...(mustMentionItems.length ? { mustMentionItems } : {}),
        ...(additionalRecipientNames ? { additionalRecipientNames } : {}),
        ...(additionalSenderNames ? { additionalSenderNames } : {}),
      },
    }),
    delay(2200),
  ]);

  preparing.value = false;
  await navigateTo('/checkout');
}

function addNameDetail(kind: 'recipient' | 'from') {
  nameDetails[kind].push({ name: '', role: '' });
}

function removeNameDetail(kind: 'recipient' | 'from', index: number) {
  nameDetails[kind].splice(index, 1);
}

function addItem(fieldName: 'anecdotes' | 'mustMention') {
  itemDetails[fieldName].push('');
  nextTick(() => document.getElementById(`input-${fieldName}-${itemDetails[fieldName].length - 1}`)?.focus());
}

function removeItem(fieldName: 'anecdotes' | 'mustMention', index: number) {
  itemDetails[fieldName].splice(index, 1);
  if (!itemDetails[fieldName].length) {
    itemDetails[fieldName].push('');
  }
}
</script>

<template>
  <div class="rich-card overflow-hidden" :style="themeStyle">
    <div v-if="preparing" class="relative min-h-[560px] overflow-hidden p-8 sm:p-12">
      <div class="absolute inset-0 opacity-70" :style="{ background: 'linear-gradient(135deg, var(--accent-soft), var(--color-surface), var(--color-surface-soft))' }" />
      <div class="relative mx-auto flex min-h-[480px] max-w-3xl flex-col items-center justify-center text-center">
        <div class="music-loader mb-8">
          <span v-for="n in 5" :key="n" :style="{ animationDelay: `${n * 0.12}s` }" />
        </div>
        <span class="section-kicker">Songtekst voorbereiden</span>
        <h2 class="font-display text-3xl font-semibold leading-tight sm:text-5xl" :style="{ color: 'var(--color-ink)' }">
          We zetten je briefing om naar lyrics
        </h2>
        <p class="mt-5 max-w-xl text-base leading-8" :style="{ color: 'var(--color-ink-soft)' }">
          We ordenen de situaties, kiezen passende details voor coupletten en bouwen alvast een preview voor de checkout.
        </p>

        <div class="mt-9 grid w-full gap-3 sm:grid-cols-5">
          <div
            v-for="(word, index) in loadingWords"
            :key="word"
            class="lyric-tile rounded-xl border px-3 py-4 text-xs font-bold uppercase tracking-[0.14em]"
            :style="{ animationDelay: `${index * 0.16}s`, borderColor: 'var(--color-line)', background: 'rgba(255,255,255,0.74)', color: 'var(--color-ink-soft)' }"
          >
            {{ word }}
          </div>
        </div>
      </div>
    </div>

    <form v-else @submit.prevent="nextStep" novalidate>
      <section v-if="!started" class="grid gap-0 lg:grid-cols-[0.78fr_1.22fr]">
        <aside class="border-b p-6 sm:p-8 lg:border-b-0 lg:border-r" :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }">
          <span class="section-kicker">Briefing</span>
          <h2 class="font-display text-2xl font-semibold leading-tight" :style="{ color: 'var(--color-ink)' }">
            Vertel stap voor stap wat dit nummer herkenbaar maakt
          </h2>
          <p class="mt-3 text-sm leading-7" :style="{ color: 'var(--color-ink-soft)' }">
            Je krijgt steeds één duidelijke vraag. Zo blijft het kort, maar verzamelen we genoeg details voor een persoonlijke tekst.
          </p>
        </aside>

        <div class="flex min-h-[460px] flex-col justify-center p-6 sm:p-10">
          <span class="text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">
            {{ fields.length }} korte stappen
          </span>
          <h3 class="mt-4 font-display text-3xl font-semibold leading-tight sm:text-5xl" :style="{ color: 'var(--color-ink)' }">
            Start je aanvraag voor {{ category.title.toLowerCase() }}
          </h3>
          <p class="mt-5 max-w-2xl text-base leading-8" :style="{ color: 'var(--color-ink-soft)' }">
            Denk aan echte namen, situaties, uitspraken en muzikale voorkeuren. Je kunt later bij de checkout nog zien hoe lang de lyrics worden.
          </p>

          <div class="mt-8 grid gap-3 text-sm sm:grid-cols-3" :style="{ color: 'var(--color-ink-soft)' }">
            <div class="metric-tile">1 vraag per scherm</div>
            <div class="metric-tile">Losse situaties met plusjes</div>
            <div class="metric-tile"><OfferBadge size="sm" inline /></div>
          </div>

          <button type="button" class="stitch-button mt-9 w-full py-4 text-base sm:w-auto sm:px-10" @click="start">
            Start aanvraag
          </button>
        </div>
      </section>

      <section v-else-if="activeField" class="min-h-[620px]">
        <div class="border-b p-5 sm:p-7" :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }">
          <div class="mb-3 flex items-center justify-between gap-4">
            <span class="text-xs font-bold uppercase tracking-[0.18em]" :style="{ color: 'var(--accent-strong)' }">
              {{ activeSection.label }} · stap {{ currentIndex + 1 }} van {{ fields.length }}
            </span>
            <span class="text-sm font-bold" :style="{ color: 'var(--color-ink-soft)' }">{{ progress }}%</span>
          </div>
          <div class="h-2 overflow-hidden rounded-full" :style="{ background: 'var(--color-line)' }">
            <div class="h-full rounded-full transition-all duration-500" :style="{ width: `${progress}%`, background: 'var(--accent)' }" />
          </div>
        </div>

        <div class="grid gap-0 lg:grid-cols-[0.82fr_1.18fr]">
          <aside class="border-b p-6 sm:p-8 lg:border-b-0 lg:border-r" :style="{ borderColor: 'var(--color-line)' }">
            <span class="section-kicker">{{ activeSection.label }}</span>
            <h2 class="font-display text-3xl font-semibold leading-tight sm:text-4xl" :style="{ color: 'var(--color-ink)' }">
              {{ questionTitle(activeField) }}
            </h2>
            <p class="mt-4 text-base leading-8" :style="{ color: 'var(--color-ink-soft)' }">
              {{ questionCopy(activeField) }}
            </p>
            <p v-if="exampleCopy(activeField)" class="mt-5 rounded-xl border p-4 text-sm leading-7" :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)', color: 'var(--color-ink-soft)' }">
              {{ exampleCopy(activeField) }}
            </p>
          </aside>

          <div :id="`field-${activeField.name}`" class="flex min-h-[500px] flex-col justify-center p-6 sm:p-10">
            <label :for="`input-${activeField.name}`" class="field-label">
              {{ activeField.label }}<span v-if="activeField.required" :style="{ color: 'var(--accent-strong)' }"> *</span>
            </label>

            <div v-if="isRepeatableItemField(activeField.name)" class="space-y-4">
              <div
                v-for="(item, index) in itemDetails[activeField.name]"
                :key="`${activeField.name}-${index}`"
                class="rounded-xl border p-4"
                :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }"
              >
                <div class="mb-2 flex items-center justify-between gap-3">
                  <span class="text-xs font-bold uppercase tracking-[0.14em]" :style="{ color: 'var(--color-ink-faint)' }">
                    {{ itemFieldLabels[activeField.name].singular }} {{ index + 1 }}
                  </span>
                  <button
                    v-if="itemDetails[activeField.name].length > 1"
                    type="button"
                    class="ghost-button px-3 py-1.5 text-xs"
                    @click="removeItem(activeField.name, index)"
                  >
                    Verwijder
                  </button>
                </div>
                <textarea
                  :id="`input-${activeField.name}-${index}`"
                  v-model="itemDetails[activeField.name][index]"
                  rows="4"
                  class="field-input bg-white text-base"
                  :placeholder="itemFieldLabels[activeField.name].placeholder"
                />
              </div>

              <button
                type="button"
                class="add-pill"
                @click="addItem(activeField.name)"
              >
                <span class="text-lg leading-none">+</span>
                {{ itemFieldLabels[activeField.name].add }}
              </button>
            </div>

            <textarea
              v-else-if="activeField.type === 'textarea'"
              :id="`input-${activeField.name}`"
              v-model="values[activeField.name]"
              rows="7"
              class="field-input text-base"
              :placeholder="activeField.placeholder"
            />

            <div v-else-if="activeField.type === 'select'" class="grid max-h-[360px] gap-3 overflow-y-auto pr-1 sm:grid-cols-2">
              <button
                v-for="opt in activeField.options"
                :key="opt"
                type="button"
                class="option-pill"
                :aria-pressed="values[activeField.name] === opt"
                @click="values[activeField.name] = opt; errors[activeField.name] = false"
              >
                {{ opt }}
              </button>
            </div>

            <input
              v-else
              :id="`input-${activeField.name}`"
              v-model="values[activeField.name]"
              :type="activeField.type"
              class="field-input text-base"
              :placeholder="activeField.placeholder"
              @input="errors[activeField.name] = false"
            />

            <div v-if="activeField.name === 'recipientName' || activeField.name === 'fromName'" class="mt-5 space-y-3">
              <div
                v-for="(row, index) in activeField.name === 'recipientName' ? nameDetails.recipient : nameDetails.from"
                :key="`${activeField.name}-${index}`"
                class="grid gap-2 rounded-xl border p-3 sm:grid-cols-[1fr_1fr_auto]"
                :style="{ borderColor: 'var(--color-line)', background: 'var(--color-surface-soft)' }"
              >
                <input v-model="row.name" class="field-input bg-white py-3" placeholder="Extra naam" type="text" />
                <input v-model="row.role" class="field-input bg-white py-3" placeholder="Rol / relatie / soort" type="text" />
                <button
                  type="button"
                  class="ghost-button px-3 py-2"
                  @click="removeNameDetail(activeField.name === 'recipientName' ? 'recipient' : 'from', index)"
                >
                  Verwijder
                </button>
              </div>
              <button
                type="button"
                class="add-pill"
                @click="addNameDetail(activeField.name === 'recipientName' ? 'recipient' : 'from')"
              >
                <span class="text-lg leading-none">+</span>
                Naam toevoegen
              </button>
            </div>

            <p v-if="errors[activeField.name]" class="mt-3 text-sm font-semibold" :style="{ color: '#c0392b' }">
              Vul dit veld in om verder te gaan.
            </p>

            <div class="mt-10 flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between" :style="{ borderColor: 'var(--color-line)' }">
              <button type="button" class="ghost-button px-5 py-3" :disabled="currentIndex === 0" @click="previousStep">
                Terug
              </button>
              <button type="submit" class="stitch-button px-8 py-3.5">
                {{ isLastStep ? 'Naar afronden' : 'Volgende' }}
              </button>
            </div>
          </div>
        </div>
      </section>
    </form>
  </div>
</template>

<style scoped>
.music-loader {
  display: flex;
  align-items: end;
  gap: 10px;
  height: 72px;
}

.music-loader span {
  width: 12px;
  height: 28px;
  border-radius: 999px;
  background: var(--accent);
  animation: music-wave 0.9s ease-in-out infinite alternate;
}

.lyric-tile {
  animation: lyric-float 1.8s ease-in-out infinite;
}

@keyframes music-wave {
  from {
    height: 22px;
    opacity: 0.55;
  }
  to {
    height: 72px;
    opacity: 1;
  }
}

@keyframes lyric-float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}
</style>
