import { createContentLoader } from 'vitepress'

export default createContentLoader('blog/**/index.md', {
    transform(posts) {
        return posts
            .map(page => {
                const match = page.url.match(/blog\/(\d{4})\/(\d{2})\/(\d{2})/)

                if (match) {
                    const date = new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]))
                    return {
                        url: page.url,
                        title: page.frontmatter.title,
                        fdate: date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }),
                        date: `${match[1]}-${match[2]}-${match[3]}`,
                        teaser: page.frontmatter.teaser,
                    }
                } else {
                    return null
                }
            })
            .filter(Boolean)
            .sort((a, b) => new Date(b.date) - new Date(a.date))
    }
})
