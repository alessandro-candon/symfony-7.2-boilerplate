module.exports = {
    extends: ['@commitlint/config-conventional'],
    rules: {
        'subject-case': [
            2,
            'always',
            'lower-case'
        ],
        'type-enum': [
            2,
            'always',
            [
                'feat',
                'fix',
                'docs',
                'chore',
                'style',
                'refactor',
                'ci',
                'test',
                'revert',
                'perf',
                'vercel',
            ],
        ],
    },
};