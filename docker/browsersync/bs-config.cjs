module.exports = {
  proxy: 'http://web:80',
  listen: '0.0.0.0',
  port: 3000,
  files: ['/workspace/public/**/*.{php,html,css,js}'],
  watchOptions: { ignoreInitial: true },
  injectChanges: true,
  // Inject after the opening body even when a PHP error truncates the response.
  snippetOptions: {
    rule: { match: /<body[^>]*>/i, fn: (snippet, match) => match + snippet }
  },
  open: false,
  ui: false,
  online: false,
  ghostMode: false,
  notify: false,
  logLevel: 'debug'
};
