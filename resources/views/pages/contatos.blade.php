@extends('layouts.landing')
@section('title', 'Contato - Alphaview Serviços Residenciais')

@section('content')
<section class="pt-32 pb-16" style="background-color: #212529;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Fale Conosco</h1>
        <p class="text-lg text-gray-400 max-w-2xl mx-auto">Estamos aqui para ajudar. Entre em contato conosco.</p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Envie sua mensagem</h2>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all" placeholder="Seu nome">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all" placeholder="seu@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assunto</label>
                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all" placeholder="Assunto da mensagem">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                        <textarea rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#f4623a] focus:border-transparent outline-none transition-all resize-none" placeholder="Escreva sua mensagem aqui..."></textarea>
                    </div>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full text-white font-semibold transition-all duration-300 hover:shadow-lg hover:scale-105" style="background-color: #f4623a;">
                        Enviar Mensagem
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="lg:pl-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Informacoes de Contato</h2>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center" style="background-color: #fff5f2;">
                            <svg class="w-6 h-6" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Email</h3>
                            <p class="text-gray-500">contato@alphaview.com</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center" style="background-color: #fff5f2;">
                            <svg class="w-6 h-6" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Localizacao</h3>
                            <p class="text-gray-500">Sao Paulo, SP - Brasil</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center" style="background-color: #fff5f2;">
                            <svg class="w-6 h-6" style="color: #f4623a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Horario de Atendimento</h3>
                            <p class="text-gray-500">Segunda a Sexta, 9h as 18h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
