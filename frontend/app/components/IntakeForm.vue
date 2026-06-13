<script setup lang="ts">
import type { Category, IntakeField } from '~/data/categories';

const props = defineProps<{ category: Category }>();

const { create } = useSongRequest();
const offer = useOffer();

// Reactieve waardes voor alle velden
const values = reactive<Record<string, string>>({});
for (const f of props.category.intakeFields) {
  values[f.name] = '';
}

const errors = reactive<Record<string, boolean>>({});
const submitting = ref(false);

function validate(): boolean {
  let ok = true;
  for (const f of props.category.intakeFields) {
    const missing = Boolean(f.required) && !values[f.name]?.trim();
    errors[f.name] = missing;
    if (missing) ok = false;
  }
  return ok;
}

async function onSubmit() {
  if (!validate()) {
    const first = props.category.intakeFields.find((f) => errors[f.name]);
    if (first) document.getElementById(`field-${first.name}`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }
  submitting.value = true;
  await create({
    category: props.category.slug,
    categoryTitle: props.category.title,
    intake: { ...values },
  });
  submitting.value = false;
  await navigateTo('/checkout');
}

function colSpan(f: IntakeField) {
  return f.span === 'full' ? 'sm:col-span-2' : '';
}
</script>

<template>
  <form class="rich-card p-6 sm:p-8" @submit.prevent="onSubmit" novalidate>
    <h2 class="font-display text-2xl font-semibold" :style="{ color: 'var(--color-ink)' }">
      Vertel ons jullie verhaal
    </h2>
    <p class="mt-2 text-sm" :style="{ color: 'var(--color-ink-soft)' }">
      Op basis hiervan schrijven we de tekst. Hoe concreter, hoe persoonlijker het nummer.
    </p>

    <div class="mt-7 grid gap-5 sm:grid-cols-2">
      <div v-for="f in category.intakeFields" :key="f.name" :id="`field-${f.name}`" :class="colSpan(f)">
        <label :for="`input-${f.name}`" class="field-label">
          {{ f.label }}<span v-if="f.required" :style="{ color: 'var(--accent-strong)' }"> *</span>
        </label>

        <textarea
          v-if="f.type === 'textarea'"
          :id="`input-${f.name}`"
          v-model="values[f.name]"
          rows="5"
          class="field-input"
          :placeholder="f.placeholder"
        />
        <select
          v-else-if="f.type === 'select'"
          :id="`input-${f.name}`"
          v-model="values[f.name]"
          class="field-input"
        >
          <option value="" disabled>Maak een keuze…</option>
          <option v-for="opt in f.options" :key="opt" :value="opt">{{ opt }}</option>
        </select>
        <input
          v-else
          :id="`input-${f.name}`"
          v-model="values[f.name]"
          :type="f.type"
          class="field-input"
          :placeholder="f.placeholder"
        />

        <p v-if="f.help" class="mt-1.5 text-xs" :style="{ color: 'var(--color-ink-faint)' }">{{ f.help }}</p>
        <p v-if="errors[f.name]" class="mt-1.5 text-xs font-semibold" :style="{ color: '#c0392b' }">
          Vul dit veld in.
        </p>
      </div>
    </div>

    <div class="mt-8 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center sm:justify-between" :style="{ borderColor: 'var(--color-line)' }">
      <div>
        <p class="text-xs uppercase tracking-[0.16em]" :style="{ color: 'var(--color-ink-faint)' }">Totaal</p>
        <OfferBadge size="sm" inline />
      </div>
      <button type="submit" class="stitch-button px-8 py-3.5" :disabled="submitting">
        {{ submitting ? 'Bezig…' : 'Naar afronden →' }}
      </button>
    </div>
  </form>
</template>
