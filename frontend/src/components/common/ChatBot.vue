<template>
  <div class="fixed bottom-8 right-8 z-[999999] font-sans">
    
    <!-- Floating Action Button — High-End Professional -->
    <button 
      @click="toggleChat" 
      class="w-16 h-16 rounded-full bg-primary text-white shadow-2xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-300 border border-white/10 group relative"
      aria-label="Contacter la conciergerie"
    >
      <div v-if="!isOpen" class="flex items-center justify-center">
        <i class="las la-sms text-2xl transition-transform group-hover:rotate-12"></i>
      </div>
      <i v-else class="las la-times text-2xl animate-fade-in"></i>

      <span v-if="!isOpen && unreadCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-accent text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-lg">
        {{ unreadCount }}
      </span>
    </button>

    <!-- Chat Hub — Elite Corporate Design -->
    <Transition name="professional-slide">
      <div 
        v-show="isOpen" 
        class="absolute bottom-20 right-0 w-[400px] max-h-[calc(100vh-120px)] h-[580px] flex flex-col bg-white border border-slate-100 rounded-[2.5rem] shadow-[0_40px_80px_-20px_rgba(0,0,0,0.15)] overflow-hidden"
      >
        <!-- Professional Header -->
        <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-white">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-accent/20 shadow-lg shadow-primary/10 overflow-hidden translate-y-0.5">
               <img src="@/assets/logo-premium.png" alt="Grand Palais" class="w-12 h-12 object-contain brightness-110" />
            </div>
            <div>
              <h3 class="font-serif text-lg font-bold text-primary tracking-tight">Conciergerie Digitale</h3>
              <div class="flex items-center gap-1.5">
                 <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                 <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">En ligne</span>
              </div>
            </div>
          </div>
          <button @click="toggleChat" class="text-slate-300 hover:text-primary transition-colors">
            <i class="las la-angle-down text-xl"></i>
          </button>
        </div>

        <!-- Conversation Stream -->
        <div ref="messageContainer" class="flex-1 overflow-y-auto px-8 py-8 space-y-6 bg-white no-scrollbar">
          <!-- Welcome Message -->
          <div class="text-center mb-8 opacity-40">
             <p class="text-[9px] uppercase font-bold tracking-[0.3em] text-slate-400">Service du Grand Palais</p>
          </div>

          <div 
            v-for="(msg, index) in messages" 
            :key="index"
            :class="[
              'max-w-[85%] p-5 rounded-3xl text-[14px] leading-relaxed transition-all duration-300',
              msg.role === 'bot' 
                ? 'bg-slate-50 text-slate-700 self-start rounded-bl-none border border-slate-100' 
                : 'bg-primary text-white self-end ml-auto rounded-br-none shadow-lg shadow-primary/10'
            ]"
          >
            <div v-html="formatMessage(msg.content)" class="prose-clean"></div>
          </div>

          <!-- Professional Typing Indicator -->
          <div v-if="isTyping" class="bg-slate-50 p-4 rounded-2xl rounded-bl-none w-16 flex gap-1 justify-center border border-slate-100 ml-1">
            <div class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce"></div>
            <div class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:0.2s]"></div>
            <div class="w-1.5 h-1.5 bg-slate-300 rounded-full animate-bounce [animation-delay:0.4s]"></div>
          </div>
        </div>

        <!-- Input Interface — Clean & Focused -->
        <div class="p-6 border-t border-slate-50 bg-white">
          <div class="relative flex items-center gap-3 bg-slate-50 rounded-2xl px-5 py-2 border border-slate-100 focus-within:border-primary/20 focus-within:bg-white transition-all">
            <textarea 
              v-model="userInput"
              @keydown.enter.prevent="sendMessage"
              placeholder="Écrivez votre message..."
              rows="1"
              class="flex-1 bg-transparent border-none py-3 text-sm focus:ring-0 outline-none text-slate-700 placeholder:text-slate-400 resize-none min-h-[44px]"
              :disabled="isTyping"
            ></textarea>
            <button 
              @click="sendMessage"
              :disabled="!userInput.trim() || isTyping"
              class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center disabled:opacity-20 transition-all hover:scale-105"
            >
              <i class="las la-paper-plane text-lg"></i>
            </button>
          </div>
          <p class="text-[8px] text-center mt-4 text-slate-300 uppercase font-bold tracking-widest">Réponse moyenne en moins d'une minute</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isOpen = ref(false)
const userInput = ref('')
const isTyping = ref(false)
const messageContainer = ref(null)
const unreadCount = ref(0)

const messages = ref([
  { 
    role: 'bot', 
    content: `${authStore.user ? 'Bonjour ' + authStore.user.name.split(' ')[0] : 'Bienvenue'}. Nous sommes ravis de vous accompagner au **Grand Palais Hotel**.` 
  }
])

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) unreadCount.value = 0
}

const scrollToBottom = async () => {
  await nextTick()
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  const msg = userInput.value.trim()
  if (!msg || isTyping.value) return

  messages.value.push({ role: 'user', content: msg })
  userInput.value = ''
  isTyping.value = true
  scrollToBottom()

  try {
    const response = await axios.post('http://localhost:5000/chat', {
      message: msg,
      history: messages.value.slice(1, -1)
    })

    messages.value.push({ 
      role: 'bot', 
      content: response.data.reply || "Désolé, je ne parviens pas à traiter votre demande." 
    })
  } catch (error) {
    messages.value.push({ 
      role: 'bot', 
      content: "Une erreur est survenue. Le service est momentanément indisponible." 
    })
  } finally {
    isTyping.value = false
    scrollToBottom()
  }
}

const formatMessage = (text) => {
  if (!text) return ''
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-primary">$1</strong>')
    .replace(/^•\s(.+)$/gm, '<li class="list-none pl-5 relative before:content-[\'•\'] before:absolute before:left-0 before:text-slate-300">$1</li>')
    .replace(/\n/g, '<br/>')
}

watch(isOpen, (newVal) => {
  if (newVal) scrollToBottom()
})

</script>

<style scoped>
.professional-slide-enter-active, .professional-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.professional-slide-enter-from, .professional-slide-leave-to {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.prose-clean :deep(strong) {
  color: #0f172a;
  font-weight: 600;
}
</style>
