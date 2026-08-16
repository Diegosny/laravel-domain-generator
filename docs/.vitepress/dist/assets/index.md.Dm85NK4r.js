import{_ as s,o as n,c as e,a2 as i}from"./chunks/framework.B1KnIGM0.js";const u=JSON.parse('{"title":"Laravel Domain Generator","description":"","frontmatter":{"layout":"home","hero":{"name":"Laravel Domain Generator","text":"DDD + Clean Architecture para Laravel","tagline":"Gere Controllers, DTOs, Services, Repositories, Resources e Migrations em segundos.","image":{"src":"/logo.svg","alt":"Laravel Domain Generator"},"actions":[{"theme":"brand","text":"Começar →","link":"/getting-started"},{"theme":"alt","text":"GitHub","link":"https://github.com/Diegosny/laravel-domain-generator"}]},"features":[{"icon":"🏛️","title":"Domain Driven Design","details":"Estruture sua aplicação por domínio desde o primeiro commit."},{"icon":"🚀","title":"CRUD Automatizado","details":"Gere Controllers, Services, DTOs, Repositories, Requests e Migrations."},{"icon":"🔐","title":"JWT Ready","details":"Login, Logout, Refresh e Me prontos para uso."},{"icon":"🔑","title":"Identificadores Públicos","details":"ULID, UUID e UUID32 configuráveis."},{"icon":"📦","title":"Repository Pattern","details":"Abstrações reutilizáveis sobre o Eloquent."},{"icon":"🎯","title":"Resources Automáticos","details":"Collections, paginação e Models transformados automaticamente."}]},"headers":[],"relativePath":"index.md","filePath":"index.md","lastUpdated":1786885288000}'),t={name:"index.md"};function l(p,a,o,r,c,d){return n(),e("div",null,[...a[0]||(a[0]=[i(`<h1 id="laravel-domain-generator" tabindex="-1">Laravel Domain Generator <a class="header-anchor" href="#laravel-domain-generator" aria-label="Permalink to &quot;Laravel Domain Generator&quot;">​</a></h1><p>Uma biblioteca para Laravel desenvolvida para automatizar e padronizar aplicações seguindo <strong>DDD</strong> e <strong>Clean Architecture</strong>.</p><h2 id="instalacao-rapida" tabindex="-1">Instalação rápida <a class="header-anchor" href="#instalacao-rapida" aria-label="Permalink to &quot;Instalação rápida&quot;">​</a></h2><div class="language-bash vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">bash</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">composer</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> require</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> domain/laravel-domain-generator</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">php</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> artisan</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> jwt:secret</span></span>
<span class="line"><span style="--shiki-light:#6F42C1;--shiki-dark:#B392F0;">php</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> artisan</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> make:domain</span><span style="--shiki-light:#032F62;--shiki-dark:#9ECBFF;"> User</span></span></code></pre></div><h2 id="fluxo-da-aplicacao" tabindex="-1">Fluxo da aplicação <a class="header-anchor" href="#fluxo-da-aplicacao" aria-label="Permalink to &quot;Fluxo da aplicação&quot;">​</a></h2><div class="language-text vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">text</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span>HTTP Request</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span> FormRequest</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span>     DTO</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span> Controller</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span>   Service</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span> Repository</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span>    Model</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span>  Resource</span></span>
<span class="line"><span>      │</span></span>
<span class="line"><span>      ▼</span></span>
<span class="line"><span> JSON Response</span></span></code></pre></div><h2 id="principais-recursos" tabindex="-1">Principais recursos <a class="header-anchor" href="#principais-recursos" aria-label="Permalink to &quot;Principais recursos&quot;">​</a></h2><ul><li>Estrutura baseada em DDD</li><li>DTO automático</li><li>Repository Pattern</li><li>Resources automáticos</li><li>JWT integrado</li><li>Hash público (ULID/UUID)</li><li>Paginação automática</li><li>Relacionamentos seguros</li><li>GitHub Actions</li><li>Compatível com Laravel 11, 12 e 13</li></ul>`,8)])])}const m=s(t,[["render",l]]);export{u as __pageData,m as default};
