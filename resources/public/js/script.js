const hljs = require('highlight.js/lib/core');

// Highlight JS
(function() {
    hljs.registerLanguage('bash', require('highlight.js/lib/languages/bash'));
    hljs.registerLanguage('plaintext', require('highlight.js/lib/languages/plaintext'));
    hljs.highlightAll();
})();
