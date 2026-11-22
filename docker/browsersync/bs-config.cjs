module.exports = {
  proxy: 'http://web:80',
  listen: '0.0.0.0',
  port: 3000,
  files: ['/workspace/public/**/*.{php,html,css,js}'],
  watchOptions: { ignoreInitial: true },
  injectChanges: true,
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
