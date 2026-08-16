import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Laravel Domain Generator',

  description: 'Generate Laravel Domains using DDD and Clean Architecture.',

  lang: 'pt-BR',

  base: '/laravel-domain-generator/',

  cleanUrls: true,

  lastUpdated: true,

  appearance: true,

  head: [
    ['link', { rel: 'icon', href: '/favicon.svg' }],
    ['meta', { property: 'og:title', content: 'Laravel Domain Generator' }],
    ['meta', { property: 'og:image', content: '/og-image.png' }]
  ],

  themeConfig: {

    logo: '/logo.svg',

    siteTitle: 'Laravel Domain Generator',

    nav: [
      {
        text: 'Início',
        link: '/'
      },
      {
        text: 'Guia',
        link: '/getting-started'
      },
      {
        text: 'GitHub',
        link: 'https://github.com/SEU_USUARIO/laravel-domain-generator'
      }
    ],

    sidebar: [

      {
        text: 'Começando',
        items: [
          {
            text: 'Visão Geral',
            link: '/'
          },
          {
            text: 'Primeiros Passos',
            link: '/getting-started'
          },
          {
            text: 'Instalação',
            link: '/installation'
          }
        ]
      },

      {
        text: 'Arquitetura',
        items: [
          {
            text: 'DDD',
            link: '/architecture'
          },
          {
            text: 'make:domain',
            link: '/make-domain'
          }
        ]
      },

      {
        text: 'Core',
        items: [
          {
            text: 'AbstractController',
            link: '/abstract-controller'
          },
          {
            text: 'AbstractService',
            link: '/abstract-service'
          },
          {
            text: 'AbstractRepository',
            link: '/abstract-repository'
          },
          {
            text: 'DTO',
            link: '/dto'
          },
          {
            text: 'Resources',
            link: '/resources'
          }
        ]
      },

      {
        text: 'Recursos',
        items: [
          {
            text: 'Hash',
            link: '/hash'
          },
          {
            text: 'JWT',
            link: '/jwt'
          }
        ]
      },

      {
        text: 'Exemplos',
        items: [
          {
            text: 'CRUD Completo',
            link: '/examples/complete-crud'
          },
          {
            text: 'Município',
            link: '/examples/municipio'
          },
          {
            text: 'Autenticação',
            link: '/examples/auth'
          }
        ]
      },

      {
        text: 'Referência',
        items: [
          {
            text: 'Configuração',
            link: '/configuration'
          },
          {
            text: 'Troubleshooting',
            link: '/troubleshooting'
          },
          {
            text: 'Roadmap',
            link: '/roadmap'
          }
        ]
      }

    ],

    search: {
      provider: 'local'
    },

    socialLinks: [
      {
        icon: 'github',
        link: 'https://github.com/Diegosny/laravel-domain-generator'
      }
    ],

    footer: {
      message: 'Released under MIT License.',
      copyright: 'Copyright © 2025'
    }

  }
})