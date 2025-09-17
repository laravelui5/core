import {defineConfig} from 'vitepress'

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
            //{text: 'Reference', link: '/docs/'},
            //{text: 'Course', link: '/course/'}
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
                {text: 'Community', link: '/guide/community'}
            ]
        },

        socialLinks: [
            {icon: 'github', link: 'https://github.com/laravelui5/core'}
        ]
    }
})
