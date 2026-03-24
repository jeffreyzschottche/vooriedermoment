<template>
  <div class="min-h-screen bg-[var(--color-surface)]">
    <div class="site-container py-8">
      <NuxtLink to="/" class="inline-flex items-center gap-3">
        <span class="logo-card">
          <img src="/logozwart.png" alt="Voor Ieder Moment" class="h-8 w-auto" />
        </span>
        <span class="font-display text-xl font-bold text-[var(--color-primary)]">Voor Ieder Moment</span>
      </NuxtLink>
    </div>

    <div class="flex items-center justify-center px-4 pb-16 pt-6">
      <div class="w-full max-w-md rounded-[1.75rem] border border-white/5 bg-[#f8f3ea] p-8 text-black shadow-[0_24px_80px_rgba(0,0,0,0.35)]">
        <slot />
      </div>
    </div>
  </div>
</template>
