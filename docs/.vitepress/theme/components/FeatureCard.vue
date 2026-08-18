<script setup lang="ts">
defineProps<{
  icon: string
  title: string
}>()
</script>

<template>
  <div class="feature-card">
    <div class="icon">{{ icon }}</div>

    <h3>{{ title }}</h3>

    <div class="content">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.feature-card {
  padding: 24px;
  border: 1px solid var(--vp-c-divider);
  border-radius: 18px;
  background: var(--vp-c-bg-soft);
  transition: .25s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  border-color: var(--vp-c-brand-1);
}

.icon {
  font-size: 28px;
  margin-bottom: 12px;
}

h3 {
  margin: 0 0 10px;
}

.content {
  color: var(--vp-c-text-2);
}
</style>