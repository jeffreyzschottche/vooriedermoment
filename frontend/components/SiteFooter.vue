<script setup lang="ts">
const footerLinks = [
  { to: '/over-ons', label: 'Over Ons' },
  { to: '/aanvraag', label: 'Aanvraag' },
  { to: '/faq-contact', label: 'FAQ & Contact' },
  { to: '/checkout', label: 'Checkout' },
];
</script>

<template>
  <footer class="mt-8 border-t border-[rgba(77,70,53,0.18)] bg-[#0d0d0d]">
    <div class="site-container py-14">
      <div class="grid gap-10 text-sm md:grid-cols-[1.2fr_1fr] md:gap-12">
        <div class="space-y-4">
          <div class="flex items-center gap-4">
            <span class="logo-card">
              <img src="/logozwart.png" alt="Voor Ieder Moment" class="h-8 w-auto" />
            </span>
            <span class="font-display text-lg font-bold text-[var(--color-primary)]">Voor Ieder Moment</span>
          </div>
          <p class="max-w-md leading-7 text-[var(--color-on-surface-muted)]">
            Uw verhaal, onze muziek. Persoonlijke composities voor de belangrijkste momenten in het leven.
          </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 md:justify-items-end">
          <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.24em] text-[var(--color-primary)]">Navigatie</p>
            <div class="flex flex-col gap-3 text-[var(--color-on-surface-muted)]">
              <NuxtLink
                v-for="link in footerLinks"
                :key="link.to"
                :to="link.to"
                class="transition hover:text-[var(--color-primary)]"
              >
                {{ link.label }}
              </NuxtLink>
            </div>
          </div>

          <div class="sm:text-right">
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.24em] text-[var(--color-primary)]">Contact</p>
            <div class="space-y-2 text-[var(--color-on-surface-muted)]">
              <p>info@vooriedermoment.nl</p>
              <p>Amsterdam, Nederland</p>
            </div>
          </div>
        </div>
      </div>

      <div class="soft-divider mt-10 mb-6" />

      <div class="flex flex-col gap-3 text-xs text-[var(--color-on-surface-muted)] sm:flex-row sm:items-center sm:justify-between">
        <p>© 2026 Voor Ieder Moment</p>
        <p>Persoonlijke muziek voor herinneringen die moeten blijven hangen.</p>
      </div>
    </div>
  </footer>
</template>
