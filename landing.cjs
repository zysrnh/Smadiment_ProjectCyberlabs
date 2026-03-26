const fs = require('fs');
let txt = fs.readFileSync('resources/views/welcome.blade.php', 'utf8');

const mainHtml = `
<main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
    <div class="flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none text-[15px] leading-relaxed" style="line-height:1.6;">
        <h1 class="mb-2 font-bold text-[#1b1b18] dark:text-[#EDEDEC]" style="font-size:32px;">SMADIMENT</h1>
        <p class="mb-8 text-[#706f6c] dark:text-[#A1A09A]">
            Aplikasi Social Media Analytics & Monitoring Dashboard komprehensif untuk memantau tren, mengukur metrik akun, serta melakukan analisis sentimen secara presisi.
        </p>
        <ul class="flex flex-col gap-4" style="margin-bottom:40px;">
            <li class="flex items-center gap-4">
                <span class="flex items-center justify-center rounded-full" style="width:32px; height:32px; background:#e0f2fe; color:#0284c7;"><svg style="width:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></span>
                <span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Real-time Trending Data</span>
            </li>
            <li class="flex items-center gap-4">
                <span class="flex items-center justify-center rounded-full" style="width:32px; height:32px; background:#e0f2fe; color:#0284c7;"><svg style="width:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></span>
                <span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Cross-platform Analytics</span>
            </li>
            <li class="flex items-center gap-4">
                <span class="flex items-center justify-center rounded-full" style="width:32px; height:32px; background:#e0f2fe; color:#0284c7;"><svg style="width:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg></span>
                <span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">AI Sentiment Analysis</span>
            </li>
        </ul>
        <a href="/user/login" class="inline-block hover:bg-black text-white leading-normal transition-all" style="background-color: #038047; padding:10px 24px; border-radius:6px; font-weight:600; text-decoration:none;">Masuk ke Dashboard Mode User</a>
        <br>
        <a href="/admin/login" class="inline-block mt-3 text-sm" style="color:#0284c7; text-decoration:underline;">atau masuk sebagai Administrator</a>
    </div>
    <div class="relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden flex flex-col items-center justify-center" style="background: linear-gradient(135deg, #038047 0%, #1e293b 100%);">
        <div class="text-white text-center p-8 z-10" style="margin-top:20px;">
            <svg style="width:90px; height:90px; margin:0 auto 20px auto; opacity:0.9;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
            <h2 style="font-size:24px; font-weight:700; margin-bottom:8px;">Welcome to Smadiment</h2>
            <p style="color:rgba(255,255,255,0.8); font-size:14px; padding:0 20px;">Discover what the world is talking about instantly.</p>
        </div>
        <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
    </div>
</main>
`;

txt = txt.replace(/<main[\s\S]*?<\/main>/, mainHtml);
fs.writeFileSync('resources/views/welcome.blade.php', txt);
console.log('Landing page replaced.');
