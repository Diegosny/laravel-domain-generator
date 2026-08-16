<script setup lang="ts">
const repo = 'Diegosny/laravel-domain-generator'
</script>

<template>
  <div class="hero-dashboard">
    <div class="card">
      <span class="label">Latest Release</span>
      <img :src="`https://img.shields.io/github/v/release/${repo}?style=flat-square`" alt="Release">
    </div>

    <div class="card">
      <span class="label">License</span>
      <img :src="`https://img.shields.io/github/license/${repo}?style=flat-square`" alt="License">
    </div>

    <div class="card">
      <span class="label">PHP</span>
      <strong>8.2+</strong>
    </div>

    <div class="card">
      <span class="label">Laravel</span>
      <strong>11 • 12 • 13</strong>
    </div>

    <div class="card">
      <span class="label">CI/CD</span>
      <strong>GitHub Actions</strong>
    </div>

    <div class="card">
      <span class="label">Documentation</span>
      <strong>VitePress</strong>
    </div>
  </div>
</template>

<style scoped>
.hero-dashboard{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:16px;
margin:32px 0;
}

.card{
padding:18px;
border:1px solid var(--vp-c-divider);
border-radius:18px;
background:var(--vp-c-bg-soft);
transition:.25s ease;
}

.card:hover{
transform:translateY(-4px);
border-color:var(--vp-c-brand-1);
}

.label{
display:block;
font-size:12px;
color:var(--vp-c-text-2);
margin-bottom:8px;
}

strong{
font-size:18px;
}
</style>