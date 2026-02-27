<div class="fixed bottom-6 right-6 z-50"> 
    <div x-data="{ 
        open: false, 
        tempMessage: '', 
        localMessages: [],
        send() {
            if(this.tempMessage.trim() === '') return;
            
            // 1. Agregamos el mensaje localmente para que aparezca DE UNA
            this.localMessages.push({
                text: this.tempMessage,
                role: 'user'
            });

            // 2. Limpiamos el input inmediatamente
            let messageToSend = this.tempMessage;
            this.tempMessage = '';

            // 3. Hacemos scroll inmediato
            $nextTick(() => { 
                const container = document.getElementById('chat-container');
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            });

            // 4. Enviamos a Livewire (Asegúrate de que tu componente reciba 'message')
            this.$wire.set('message', messageToSend);
            this.$wire.send().then(() => {
                // Cuando el servidor responde, los mensajes reales de Livewire se sincronizan
                // y limpiamos nuestra lista temporal local
                this.localMessages = [];
            });
        }
    }" class="flex flex-col items-end">
        
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-12 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-12 scale-95"
            class="w-80 sm:w-96 h-[500px] mb-4 overflow-hidden flex flex-col backdrop-blur-2xl bg-slate-900/90 border border-white/20 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)]"
            style="display: none;"
        >
            <div class="p-4 bg-gradient-to-r from-indigo-600/30 to-purple-600/30 border-b border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-3 h-3 bg-green-500 rounded-full absolute -bottom-0 -right-0 border-2 border-slate-900 animate-pulse"></div>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-xl shadow-lg">
                            🤖
                        </div>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm tracking-tight">AI Assistant</h3>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            <p class="text-[10px] text-green-400 uppercase font-bold tracking-tighter">Activo</p>
                        </div>
                    </div>
                </div>
                <button @click="open = false" class="text-white/40 hover:text-white transition-colors p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4 scroll-behavior-smooth scrollbar-hide" id="chat-container">
                @foreach ($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in-up">
                        <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm shadow-sm {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white/10 text-slate-100 border border-white/10 backdrop-blur-md rounded-tl-none' }}">
                            {!! nl2br(e($msg['text'])) !!}
                        </div>
                    </div>
                @endforeach

                <template x-for="lmsg in localMessages">
                    <div class="flex justify-end animate-fade-in-up">
                        <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm shadow-sm bg-indigo-600 text-white rounded-tr-none opacity-70">
                            <span x-text="lmsg.text"></span>
                        </div>
                    </div>
                </template>

                @if ($loading)
                    <div class="flex justify-start animate-fade-in">
                        <div class="bg-white/10 border border-white/10 backdrop-blur-md px-4 py-3 rounded-2xl rounded-tl-none flex items-center gap-1">
                            <div class="flex gap-1.5">
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce [animation-duration:0.8s]"></span>
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce [animation-delay:0.2s] [animation-duration:0.8s]"></span>
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce [animation-delay:0.4s] [animation-duration:0.8s]"></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-4 bg-white/5 border-t border-white/10">
                <form @submit.prevent="send()" class="relative flex items-center gap-2">
                    <input
                        type="text"
                        x-model="tempMessage"
                        placeholder="Escribe un mensaje..."
                        class="w-full bg-slate-800/80 text-white text-sm placeholder-slate-500 pl-4 pr-12 py-3 rounded-2xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all"
                    >
                    <button
                        type="submit"
                        :disabled="!tempMessage.trim() || $wire.loading"
                        class="absolute right-1.5 p-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition-all transform hover:scale-105 active:scale-95 disabled:opacity-30"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-45" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <button @click="open = !open" class="w-16 h-16 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-700 shadow-[0_10px_40px_rgba(79,70,229,0.4)] flex items-center justify-center text-white transition-all transform hover:scale-110 active:scale-90 relative">
            <template x-if="!open">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </template>
            <template x-if="open">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </template>
        </button>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.3s ease-out forwards; }
    </style>

    <script>
        window.addEventListener('scroll-to-bottom', () => {
            setTimeout(() => {
                const container = document.getElementById('chat-container');
                if(container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }, 50);
        });
    </script>
</div>