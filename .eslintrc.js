module.exports = {
  extends: '@studiometa/eslint-config',
  overrides: [
    {
      files: ['**/*.spec.js'],
      rules: {
        'require-jsdoc': 'off',
        'max-classes-per-file': 'off',
      },
    },
  ],
};
