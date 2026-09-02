const fs = require('fs');
const files = [
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/media-statistic.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/trending-topic.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/compare/index.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/tiktok/tiktok-emotion-analysis.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/x/most-active-users.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/x/most-retweets.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/x/overview.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/x/top-influencers.blade.php',
  'd:/Gawe/January/Smadiment_ProjectCyberlabs/resources/views/mk/ai-analysis-scripts.blade.php'
];

let changed = 0;
files.forEach(f => {
  if(!fs.existsSync(f)) return;
  let txt = fs.readFileSync(f, 'utf8');
  let orig = txt;

  if (!txt.includes('_freeze')) {
     const freezeCode = `
    function _freeze() {
        if(document.getElementById('__s_freeze')) return;
        const s = document.createElement('style'); s.id = '__s_freeze';
        s.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;animation-play-state:paused!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__s_freeze')?.remove(); }
     `;
     
     // try to insert after let _timer = null; or function _toast or function _btnState
     let inserted = false;
     txt = txt.replace(/function _btnState\(.*?\)\s*\{[\s\S]*?\}/, (match) => {
        inserted = true;
        return match + freezeCode;
     });
     
     if (!inserted) {
         txt = txt.replace(/let _timer = null;/, (match) => {
             inserted = true;
             return match + freezeCode;
         });
     }

     if (inserted) {
         txt = txt.replace(/return html2canvas\((.*?),\s*\{([\s\S]*?)\}\);/g, (match, p1, p2) => {
            return `_freeze(); await new Promise(r=>setTimeout(r,400));\n        try { return await html2canvas(${p1}, {\n${p2}\n        }); } finally { _unfreeze(); }`;
         });

         txt = txt.replace(/return await html2canvas\((.*?),\s*\{([\s\S]*?)\}\);/g, (match, p1, p2) => {
            return `_freeze(); await new Promise(r=>setTimeout(r,400));\n        try { return await html2canvas(${p1}, {\n${p2}\n        }); } finally { _unfreeze(); }`;
         });

         txt = txt.replace(/const (\w+) = await html2canvas\((.*?),\s*\{([\s\S]*?)\}\);/g, (match, p1, p2, p3) => {
            return `_freeze(); await new Promise(r=>setTimeout(r,400));\n        let ${p1};\n        try { ${p1} = await html2canvas(${p2}, {\n${p3}\n        }); } finally { _unfreeze(); }`;
         });

         txt = txt.replace(/const (capture\w+) = html2canvas\((.*?),\s*\{([\s\S]*?)\}\);/g, (match, p1, p2, p3) => {
            return `_freeze(); await new Promise(r=>setTimeout(r,400));\n        let ${p1};\n        try { ${p1} = await html2canvas(${p2}, {\n${p3}\n        }); } finally { _unfreeze(); }`;
         });
     }
  }

  if (orig !== txt) {
     fs.writeFileSync(f, txt, 'utf8');
     changed++;
  }
});
console.log('Fixed files: ' + changed);
