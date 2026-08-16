<script setup lang="ts">
defineProps<{
  version: string
}>()
</script>

<template>
  <span class="version-badge">{{ version }}</span>
</template>

<style scoped>
.version-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 999px;
  background: var(--vp-c-brand-soft);
  color: var(--vp-c-brand-1);
  font-size: 12px;
  font-weight: 600;
}
</style>