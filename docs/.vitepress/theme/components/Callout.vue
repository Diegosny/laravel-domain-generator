<script setup lang="ts">
defineProps<{
  type?: 'info' | 'success' | 'warning' | 'danger'
}>()
</script>

<template>
  <div :class="['callout', type || 'info']">
    <slot />
  </div>
</template>

<style scoped>
.callout {
  padding: 18px;
  border-left: 4px solid;
  border-radius: 10px;
  margin: 20px 0;
}

.info {
  border-color: #2563eb;
  background: rgba(37,99,235,.08);
}

.success {
  border-color: #16a34a;
  background: rgba(22,163,74,.08);
}

.warning {
  border-color: #d97706;
  background: rgba(217,119,6,.08);
}

.danger {
  border-color: #dc2626;
  background: rgba(220,38,38,.08);
}
</style>