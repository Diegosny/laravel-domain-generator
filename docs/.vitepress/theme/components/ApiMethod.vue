<script setup lang="ts">
const props = defineProps<{
  method: string
}>()

const colors = {
  GET: '#16a34a',
  POST: '#2563eb',
  PUT: '#d97706',
  PATCH: '#9333ea',
  DELETE: '#dc2626'
}
</script>

<template>
  <span
    class="method"
    :style="{ backgroundColor: colors[props.method] || '#64748b' }"
  >
    {{ method }}
  </span>
</template>

<style scoped>
.method {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  color: white;
  font-weight: 700;
  font-size: 12px;
}
</style>