module.exports = {
  plugins: [
    'stylelint-scss'
  ],
  rules: {
    'at-rule-empty-line-before': ['always', {
      except: 'blockless-after-same-name-blockless'
    }],
    'block-opening-brace-space-before': 'always',
    'comment-no-empty': true,
    'comment-whitespace-inside': 'always',
    'comment-empty-line-before': ['always', {
      ignore: ['stylelint-commands']
    }],
    'declaration-colon-space-after': 'always',
    'declaration-colon-space-before': 'never',
    indentation: 2,
    'max-nesting-depth': 4,
    'no-invalid-double-slash-comments': true,
    'selector-list-comma-newline-after': 'always',
    'selector-max-id': 0,

    'scss/dollar-variable-pattern': '[a-z]+(-[a-z]+)*',
    'scss/at-extend-no-missing-placeholder': true,

    'declaration-property-value-blacklist': {
      '/^border$/': ['none']
    }
  }
};