import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Laravel Domain Generator',
  description: 'Generate Laravel domains following DDD and Clean Architecture.',

  lang: 'en-US',

  head: [
    ['link', { rel: 'icon', href: '/favicon.svg' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:title', content: 'Laravel Domain Generator' }],
    ['meta', { property: 'og:description', content: 'Generate Laravel domains following DDD and Clean Architecture.' }],
    ['meta', { property: 'og:image', content: '/og-image.png' }]
  ],

  locales: {
    root: {
      label: 'English',
      lang: 'en-US',
      link: '/'
    },
    pt: {
      label: 'Português',
      lang: 'pt-BR',
      link: '/pt/'
    }
  },

  themeConfig: {
    logo: '/logo.svg',

    search: {
      provider: 'local'
    },

    socialLinks: [
      {
        icon: 'github',
        link: 'https://github.com/Diegosny/laravel-domain-generator'
      }
    ],

    editLink: {
      pattern: 'https://github.com/Diegosny/laravel-domain-generator/edit/master/docs/:path',
      text: 'Edit this page on GitHub'
    },

    outline: {
      level: [2, 3],
      label: 'On this page'
    },

    docFooter: {
      prev: 'Previous',
      next: 'Next'
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2025 Diego Camilo'
    },

    nav: [
      {
        text: 'Guide',
        activeMatch: '^/(getting-started|installation|architecture|make-domain)',
        items: [
          { text: 'Getting Started', link: '/getting-started' },
          { text: 'Installation', link: '/installation' },
          { text: 'Architecture', link: '/architecture' }
        ]
      },
      {
        text: 'Examples',
        activeMatch: '^/examples/',
        items: [
          { text: 'Complete CRUD', link: '/examples/complete-crud' },
          { text: 'Municipio', link: '/examples/municipio' },
          { text: 'Authentication', link: '/examples/auth' }
        ]
      },
      {
        text: 'API',
        activeMatch: '^/(jwt|resources|dto)',
        items: [
          { text: 'JWT', link: '/jwt' },
          { text: 'Resources', link: '/resources' },
          { text: 'DTO', link: '/dto' }
        ]
      },
      {
        text: 'GitHub ↗',
        link: 'https://github.com/Diegosny/laravel-domain-generator'
      }
    ],

    sidebar: {
      '/': [
        {
          text: 'Getting Started',
          collapsed: false,
          items: [
            { text: 'Overview', link: '/' },
            { text: 'Getting Started', link: '/getting-started' },
            { text: 'Installation', link: '/installation' }
          ]
        },
        {
          text: 'Architecture',
          collapsed: false,
          items: [
            { text: 'DDD & Clean Architecture', link: '/architecture' },
            { text: 'make:domain', link: '/make-domain' }
          ]
        },
        {
          text: 'Core',
          collapsed: false,
          items: [
            { text: 'AbstractController', link: '/abstract-controller' },
            { text: 'AbstractService', link: '/abstract-service' },
            { text: 'AbstractRepository', link: '/abstract-repository' },
            { text: 'DTO', link: '/dto' },
            { text: 'Resources', link: '/resources' }
          ]
        },
        {
          text: 'Resources',
          collapsed: false,
          items: [
            { text: 'Public Identifiers', link: '/hash' },
            { text: 'JWT', link: '/jwt' },
            { text: 'Configuration', link: '/configuration' }
          ]
        },
        {
          text: 'Examples',
          collapsed: false,
          items: [
            { text: 'Complete CRUD', link: '/examples/complete-crud' },
            { text: 'Municipio', link: '/examples/municipio' },
            { text: 'Authentication', link: '/examples/auth' }
          ]
        },
        {
          text: 'Project',
          collapsed: false,
          items: [
            { text: 'Troubleshooting', link: '/troubleshooting' },
            { text: 'Contributing', link: '/contributing' },
            { text: 'Roadmap', link: '/roadmap' }
          ]
        }
      ],

      '/pt/': [
        {
          text: 'Começando',
          collapsed: false,
          items: [
            { text: 'Visão Geral', link: '/pt/' },
            { text: 'Primeiros Passos', link: '/pt/getting-started' },
            { text: 'Instalação', link: '/pt/installation' }
          ]
        },
        {
          text: 'Arquitetura',
          collapsed: false,
          items: [
            { text: 'DDD e Clean Architecture', link: '/pt/architecture' },
            { text: 'make:domain', link: '/pt/make-domain' }
          ]
        },
        {
          text: 'Core',
          collapsed: false,
          items: [
            { text: 'AbstractController', link: '/pt/abstract-controller' },
            { text: 'AbstractService', link: '/pt/abstract-service' },
            { text: 'AbstractRepository', link: '/pt/abstract-repository' },
            { text: 'DTO', link: '/pt/dto' },
            { text: 'Resources', link: '/pt/resources' }
          ]
        },
        {
          text: 'Recursos',
          collapsed: false,
          items: [
            { text: 'Identificadores Públicos', link: '/pt/hash' },
            { text: 'JWT', link: '/pt/jwt' },
            { text: 'Configuração', link: '/pt/configuration' }
          ]
        },
        {
          text: 'Exemplos',
          collapsed: false,
          items: [
            { text: 'CRUD Completo', link: '/pt/examples/complete-crud' },
            { text: 'Município', link: '/pt/examples/municipio' },
            { text: 'Autenticação', link: '/pt/examples/auth' }
          ]
        },
        {
          text: 'Projeto',
          collapsed: false,
          items: [
            { text: 'Troubleshooting', link: '/pt/troubleshooting' },
            { text: 'Contribuindo', link: '/pt/contributing' },
            { text: 'Roadmap', link: '/pt/roadmap' }
          ]
        }
      ]
    }
  }
})