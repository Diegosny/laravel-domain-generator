import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Laravel Domain Generator',

  description: 'Generate Laravel domains using DDD and Clean Architecture.',

  lang: 'pt-BR',

  base: '/laravel-domain-generator/',

  cleanUrls: true,

  lastUpdated: true,

  appearance: true,

  ignoreDeadLinks: true,

  head: [
    ['link', { rel: 'icon', href: '/favicon.svg' }],

    ['meta', { name: 'theme-color', content: '#E11D48' }],

    ['meta', { property: 'og:type', content: 'website' }],

    ['meta', { property: 'og:title', content: 'Laravel Domain Generator' }],

    ['meta', { property: 'og:description', content: 'Generate Laravel domains using DDD and Clean Architecture.' }],

    ['meta', { property: 'og:image', content: '/og-image.png' }],

    ['meta', { name: 'twitter:card', content: 'summary_large_image' }],

    ['meta', { name: 'twitter:title', content: 'Laravel Domain Generator' }],

    ['meta', { name: 'twitter:description', content: 'Generate Laravel domains using DDD and Clean Architecture.' }],

    ['meta', { name: 'twitter:image', content: '/og-image.png' }]
  ],

  themeConfig: {
    logo: '/logo.svg',
    siteTitle: 'Laravel Domain Generator',

  nav: [
    { text: 'Guia', link: '/getting-started' },
    { text: 'Exemplos', link: '/examples/complete-crud' },
    { text: 'API', link: '/abstract-controller' },
    {
      text: 'GitHub',
      link: 'https://github.com/Diegosny/laravel-domain-generator'
    }

  ],
    sidebar: [
      {
        text: 'Começando',

        collapsed: false,

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

        collapsed: false,

        items: [
          {
            text: 'DDD e Clean Architecture',
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

        collapsed: false,

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

        collapsed: false,

        items: [
          {
            text: 'Identificadores Públicos',
            link: '/hash'
          },

          {
            text: 'JWT',
            link: '/jwt'
          },

          {
            text: 'Configuração',
            link: '/configuration'
          }
        ]
      },

      {
        text: 'Exemplos',

        collapsed: false,

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
        text: 'Projeto',

        collapsed: false,

        items: [
          {
            text: 'Troubleshooting',
            link: '/troubleshooting'
          },

          {
            text: 'Contribuindo',
            link: '/contributing'
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
        link: 'https://github.com/SEU_USUARIO/laravel-domain-generator'
      }
    ],

    editLink: {
      pattern: 'https://github.com/SEU_USUARIO/laravel-domain-generator/edit/master/docs/:path',

      text: 'Editar esta página no GitHub'
    },

    outline: {
      level: [2, 3],

      label: 'Nesta página'
    },

    docFooter: {
      prev: 'Página anterior',

      next: 'Próxima página'
    },

    footer: {
      message: 'Distribuído sob a licença MIT.',

      copyright: 'Copyright © 2025 Laravel Domain Generator'
    }
  }
})