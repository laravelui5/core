import {defineConfig} from 'vitepress'
import tailwindcss from '@tailwindcss/vite'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: "LaravelUi5",
    description: "The Lean Enterprise Stack",
    publicDir: "public",
    themeConfig: {
        logo: '/logo-icon.svg',

        // https://vitepress.dev/reference/default-theme-config
        nav: [
            {text: 'Home', link: '/'},
            {text: 'Guide', link: '/guide/'},
            //{text: 'Blog', link: '/blog/'},
            {text: 'Course', link: '/course/'}
        ],

        footer: {
            message: 'Released under the Apache 2.0 License.',
            copyright: 'Copyright © 2025-present Michael Gerzabek, Pragmatiqu IT GmbH'
        },

        search: {
            provider: 'local'
        },

        sidebar: {
            '/guide/': [
                {
                    text: 'Introduction',
                    items: [
                        {text: 'Who is LaravelUi5 for?', link: '/guide/target-audience'},
                        {
                            text: 'Example Use Cases',
                            items: [
                                {text: 'Laravel Agency Delivering a Custom HR Dashboard', link: '/guide/use-case/agency'},
                                {text: 'IT Team', link: '/guide/use-case/it-team'},
                                {text: 'SaaS Startup', link: '/guide/use-case/saas'}
                            ]
                        },
                        {text: 'Why UI5 over Blade?', link: '/guide/why-ui5-over-blade'},
                        {text: 'Architecture Overview', link: '/guide/architecture'}
                    ],
                },
                {
                    text: 'Getting Started',
                    items: [
                        {text: 'Quickstart', link: '/guide/quickstart'},
                        {text: 'Installation', link: '/guide/installation'},
                        {text: 'Landscape', link: '/guide/landscape'}
                    ],
                },
                {
                    text: 'Backend Anatomy',
                    items: [
                        {text: 'Overview', link: '/guide/backend/index'},
                        {text: 'Ui5Module', link: '/guide/backend/module'},
                        {text: 'Ui5Library', link: '/guide/backend/library'},
                        {text: 'Ui5App', link: '/guide/backend/app'},
                        {text: 'Ui5App self contained', link: '/guide/backend/self-contained'},
                        {text: 'Ui5Resource', link: '/guide/backend/resource'},
                        {text: 'Ui5Card', link: '/guide/backend/card'},
                        {text: 'Ui5Report', link: '/guide/backend/report'},
                        {text: 'Ui5Tile', link: '/guide/backend/tile'},
                        {text: 'Ui5KPI', link: '/guide/backend/kpi'},
                        {text: 'Ui5Dashboard', link: '/guide/backend/dashboard'},
                        {text: 'Ui5Action', link: '/guide/backend/action'},
                        {text: 'Ui5Registry', link: '/guide/backend/registry'}
                    ]
                },
                {text: 'Frontend Layer', link: '/guide/frontend/index' },
                //items: [
                //  {text: 'Overview', link: '/guide/frontend/index'},
                //{text: 'Facade', link: '/guide/frontend/facade'},
                //{text: 'Connection', link: '/guide/frontend/connection'},
                //]
                //},
                {text: 'Resources', link: '/guide/resources'},
                {text: 'Community', link: '/guide/community'},
                {text: 'API Reference', link: '/api/index.html', target: '_blank'}
            ],
            '/course/': [
                {
                    text: 'Video Course',
                    items: [
                        {
                            text: 'Introduction',
                            items: [
                                { text: 'Welcome (3:35)', link: '/course/video-01' },
                            ]
                        },
                        {
                            text: 'Getting Set Up',
                            items: [
                                { text: 'Installation (5:47)', link: '/course/video-02' },
                                { text: 'Core Setup (7:24)', link: '/course/video-03' },
                                { text: 'Scaffolding an App (12:34)', link: '/course/video-04' },
                                { text: 'Enabling OData (8:52)', link: '/course/video-05' },
                                { text: 'Configuration (7:49)', link: '/course/video-06' },
                            ]
                        },
                        {
                            text: 'Building the UI',
                            items: [
                                { text: 'The Master View (15:08)', link: '/course/video-07' },
                                { text: 'Live Search (17:16)', link: '/course/video-08' },
                                { text: 'The Detail Page (13:16)', link: '/course/video-09' },
                                { text: 'SAP & the Rest of Us (6:53)', link: '/course/video-10' },
                                { text: 'The Lock Function (24:55)', link: '/course/video-11' },
                            ]
                        },
                        {
                            text: 'Wrap-Up',
                            items: [
                                { text: 'Just the Beginning (10:20)', link: '/course/video-12' },
                            ]
                        }
                    ],
                }
            ]
        },

        socialLinks: [
            {icon: 'github', link: 'https://github.com/laravelui5/core'}
        ]
    },

    vite: {
        plugins: [tailwindcss()],
    }
})
