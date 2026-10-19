<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAEPVision</title>
    <!-- Fonte Inter Obrigatória -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> 
        body { font-family: 'Inter', sans-serif; }
        { display: none !important; }
    </style>
</head>
<body class="bg-white text-black min-h-screen flex flex-col justify-between" x-data="galleryApp()" x-init="fetchPhotos()">
    <!-- 1. Cabeçalho da Aplicação -->
    <header class="flex flex-col md:flex-row justify-between items-center px-4 sm:px-6 py-4 border-b border-gray-200 gap-4 md:gap-0">
        
        <!-- 1.2 Seção Empresa (Centralizada em mobile, ordem principal no topo em telas pequenas) -->
        <div class="w-full md:w-1/3 text-center order-1 md:order-2">
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">SAEPVision</h1>
        </div>

        <!-- 1.1 Seção Usuário -->
        <div class="flex items-center justify-center md:justify-start gap-3 w-full md:w-1/3 order-2 md:order-1">
            <template x-if="user">
                <div class="flex items-center gap-2 sm:gap-3">
                    <img :src="user.avatar ? '/storage/' + user.avatar : 'images/default-user.webp'" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border border-black object-cover shrink-0">
                    <span class="font-semibold text-sm sm:text-base truncate max-w-[100px] sm:max-w-[150px]" x-text="'@' + user.name"></span>
                    <button 
                        @click="openProfileModal = true" 
                        :disabled="user.role !== 'fotografo'"
                        class="bg-black text-white px-2.5 sm:px-3 py-1 text-xs sm:text-sm font-semibold rounded disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                        Ver Perfil
                    </button>
                </div>
            </template>
        </div>

        <!-- 1.3 Seção de Login / Logout -->
        <div class="w-full md:w-1/3 flex justify-center md:justify-end order-3">
            <template x-if="!user">
                <button @click="showLoginModal = true; authMode = 'login';" class="w-full sm:w-auto bg-black text-white px-5 py-2 rounded font-semibold hover:bg-gray-800 transition text-sm sm:text-base">
                    Login
                </button>
            </template>
            <template x-if="user">
                <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-right">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto bg-black text-white px-5 py-2 rounded font-semibold hover:bg-red-600 transition text-sm sm:text-base">
                        Sair
                    </button>
                </form>
            </template>
        </div>
    </header>

    <!-- Menu Lateral (Perfil do Fotógrafo) -->
    <div x-show="openProfileModal" class="fixed inset-y-0 left-0 w-full sm:w-80 bg-gray-100 shadow-2xl p-6 z-40 border-r border-gray-300 transition-all">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Perfil do Fotógrafo</h2>
            <button @click="openProfileModal = false" class="text-black font-bold text-2xl hover:opacity-70">&times;</button>
        </div>
        <div class="space-y-4">
            <p class="text-lg"><span class="font-bold" x-text="userStats.posts"></span> Likes</p>
            <p class="text-lg border-t border-b border-black py-2"><span class="font-bold" x-text="userStats.likes"></span> Publicações</p>
        </div>
    </div>

    <!-- 2. Corpo da Aplicação -->
    <main class="max-w-6xl w-full mx-auto py-6 sm:py-8 px-4 flex-grow">

        <!-- Seção Exclusiva para Fotógrafos: Formulário de Nova Foto -->
        <template x-if="user && user.role === 'fotografo'">
            <div class="mb-8 p-4 sm:p-6 bg-gray-50 border border-black rounded-lg max-w-xl mx-auto shadow-sm">
                <h2 class="text-base sm:text-lg font-bold mb-4">Nova Publicação</h2>

                <form @submit.prevent="uploadPhoto()" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Título da Foto</label>
                        <input type="text" x-model="photoForm.title" placeholder="Ex: Pôr do sol na praia" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Localização</label>
                        <input type="text" x-model="photoForm.location" placeholder="Ex: Rio de Janeiro, RJ" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Imagem</label>
                        <input type="file" @change="photoForm.image = $event.target.files[0]" accept="image/*" required class="w-full border border-black rounded p-2 text-sm bg-white">
                    </div>

                    <button type="submit" class="w-full bg-black text-white font-semibold py-2 rounded hover:bg-gray-800 transition text-sm sm:text-base">
                        Publicar Foto
                    </button>
                </form>
            </div>
        </template>

        <!-- 2.1 Campo de Busca -->
        <div class="relative mb-8 max-w-xl mx-auto">
            <input 
                type="text" 
                x-model="search" 
                @input.debounce.300ms="fetchPhotos()"
                placeholder="Digite o nome do fotógrafo... ex: @fotografo1" 
                class="w-full border border-black rounded p-3 pr-12 text-sm sm:text-base focus:outline-none focus:ring-1 focus:ring-black">
                <img src="/images/lupa.svg" class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 opacity-70">
        </div>

        <!-- Mensagem de Erro na Busca -->
        <template x-if="searchError">
            <p class="text-center text-red-600 font-semibold mb-6 text-sm sm:text-base" x-text="searchError"></p>
        </template>

        <!-- 2.2 Galeria de Fotos (1 col no mobile, 2 em tablets e 3 em desktop) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
            <template x-for="photo in photos" :key="photo.id">
                <div class="relative group border border-gray-300 rounded overflow-hidden shadow-sm hover:shadow-md transition">
                    <!-- Tooltip no Hover com Informações da Foto -->
                    <div class="absolute inset-0 bg-black/75 opacity-0 group-hover:opacity-100 transition-opacity p-4 text-white flex flex-col justify-end pointer-events-none z-10">
                        <p class="text-xs sm:text-sm font-semibold" x-text="'Foto tirada por: ' + photo.photographer"></p>
                        <p class="text-xs sm:text-sm" x-text="'Local: ' + photo.location"></p>
                    </div>

                    <!-- Imagem -->
                    <img :src="photo.image_url" class="w-full h-56 sm:h-64 object-cover">

                    <!-- Informações e Curtidas abaixo da foto -->
                    <div class="p-3 sm:p-4 flex justify-between items-center bg-white border-t border-gray-100">
                        <span class="font-semibold text-gray-800 text-sm sm:text-base truncate mr-2" x-text="photo.title"></span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button @click="toggleLike(photo)" class="focus:outline-none">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-colors" :class="photo.is_liked ? 'text-red-600 fill-current' : 'text-black fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            <span class="font-bold text-xs sm:text-sm" x-text="photo.likes_count"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Modal de Autenticação (Login / Registro) com Breeze -->
    <div 
        x-show="showLoginModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-5 sm:p-6 relative border border-black max-h-[90vh] overflow-y-auto">
            
            <!-- Botão Fechar (X) -->
            <button 
                @click="showLoginModal = false" 
                class="absolute top-4 right-4 text-black font-bold text-2xl hover:opacity-70"
            >
                &times;
            </button>

            <!-- Abas do Modal -->
            <div class="flex border-b border-gray-300 mb-6">
                <button 
                    type="button"
                    @click="authMode = 'login'" 
                    :class="authMode === 'login' ? 'border-b-2 border-black font-bold text-black' : 'text-gray-500'" 
                    class="flex-1 py-2 text-center text-base sm:text-lg"
                >
                    Login
                </button>
                <button 
                    type="button"
                    @click="authMode = 'register'" 
                    :class="authMode === 'register' ? 'border-b-2 border-black font-bold text-black' : 'text-gray-500'" 
                    class="flex-1 py-2 text-center text-base sm:text-lg"
                >
                    Cadastre-se
                </button>
            </div>

            <!-- FORMULÁRIO DE LOGIN -->
            <form x-show="authMode === 'login'" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold mb-1">E-mail</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="Digite seu e-mail" 
                        required 
                        autofocus 
                        class="w-full border border-black rounded p-2 text-sm sm:text-base focus:outline-none focus:ring-1 focus:ring-black"
                    >
                    @if ($errors->has('email'))
                        <span class="text-red-600 text-xs mt-1 block">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold mb-1">Senha</label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        placeholder="Digite sua senha" 
                        required 
                        class="w-full border border-black rounded p-2 text-sm sm:text-base focus:outline-none focus:ring-1 focus:ring-black"
                    >
                    @if ($errors->has('password'))
                        <span class="text-red-600 text-xs mt-1 block">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                    <button 
                        type="button" 
                        @click="showLoginModal = false" 
                        class="w-full sm:w-auto border border-black px-4 py-2 rounded text-black bg-white font-semibold hover:bg-gray-100 text-sm sm:text-base"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto bg-black text-white px-6 py-2 rounded font-semibold hover:bg-gray-800 text-sm sm:text-base"
                    >
                        Login
                    </button>
                </div>
            </form>

            <!-- FORMULÁRIO DE REGISTRO -->
            <form x-show="authMode === 'register'" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-semibold mb-1">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: João Silva" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-semibold mb-1">Nome de Usuário</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Ex: joaosilva" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-semibold mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="exemplo@dominio.com" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-semibold mb-1">Tipo de Perfil</label>
                    <select name="role" class="w-full border border-black rounded p-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-black" required>
                        <option value="usuario">Usuário Comum</option>
                        <option value="fotografo">Fotógrafo</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-semibold mb-1">Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" placeholder="Repita a senha" required class="w-full border border-black rounded p-2 text-sm focus:outline-none focus:ring-1 focus:ring-black">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="mb-6">
                    <x-input-label for="avatar" value="Foto de Perfil" class="text-sm font-semibold mb-1 block" />
                    <input 
                        type="file" 
                        name="avatar" 
                        id="avatar" 
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="block w-full text-xs sm:text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:sm:text-sm file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200" 
                    />
                    <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                    <button type="button" @click="showLoginModal = false" class="w-full sm:w-auto border border-black px-4 py-2 rounded text-black font-semibold text-sm sm:text-base">Cancelar</button>
                    <button type="submit" class="w-full sm:w-auto bg-black text-white px-6 py-2 rounded font-semibold text-sm sm:text-base">Cadastrar</button>
                </div>
            </form>

        </div>
    </div>

    <!-- 3. Rodapé da Aplicação -->
    <footer class="bg-black text-white px-4 sm:px-8 py-6 sm:py-4 flex flex-col md:flex-row justify-between items-center mt-12 gap-4 md:gap-0">
        <span class="font-bold text-lg">SAEPVision</span>
        <div class="flex gap-6">
            <a href="#"><img src="/images/instagram.svg" class="w-5 h-5"></a>
            <a href="#"><img src="/images/tiktok.svg" class="w-5 h-5"></a>
            <a href="#"><img src="/images/linkedIn.svg" class="w-5 h-5"></a>
            <a href="#"><img src="/images/youtube.svg" class="w-5 h-5"></a>
        </div>
        <span class="text-xs sm:text-sm text-gray-400">Copyright 2026-2027</span>
    </footer>

    <!-- Controle de Estado Reativo com Alpine.js -->
    <script>
        function galleryApp() {
            return {
                photos: [],
                search: '',
                searchError: '',
                user: @json(auth()->user()),
                
                showLoginModal: {{ $errors->any() ? 'true' : 'false' }},
                authMode: '{{ old("name") || old("username") ? "register" : "login" }}',
                openProfileModal: false,
                stats: { likes: 0, publications: 0 },

                photoForm: {
                    title: '',
                    location: '',
                    image: null
                },

                async uploadPhoto() {
                    if (!this.photoForm.image) return;

                    let formData = new FormData();
                    formData.append('title', this.photoForm.title);
                    formData.append('location', this.photoForm.location);
                    formData.append('image', this.photoForm.image);

                    let res = await fetch('/publicar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (res.ok) {
                        this.photoForm = { title: '', location: '', image: null };
                        this.fetchPhotos();
                    } else {
                        let data = await res.json();
                        alert(data.message || 'Erro ao publicar foto.');
                    }
                },

                async fetchPhotos() {
                    this.searchError = '';
                    
                    try {
                        let res = await fetch(`/photos?search=${encodeURIComponent(this.search)}`);
                        let data = await res.json();

                        if (data.length === 0 && this.search !== '') {
                            this.searchError = 'Fotógrafo não encontrado';
                            this.photos = [];
                        } else {
                            this.photos = data;
                        }
                    } catch (error) {
                        console.error("Erro ao carregar o feed de fotos:", error);
                    }
                },

                async toggleLike(photo) {
                    if (!this.user) {
                        this.showLoginModal = true;
                        this.authMode = 'login';
                        return;
                    }
                    
                    let res = await fetch(`/photos/${photo.id}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (res.ok) {
                        let data = await res.json();
                        photo.is_liked = data.is_liked;
                        photo.likes_count = data.likes_count;
                    }
                },

                get userStats() {
                    if (!this.user) return { posts: 0, likes: 0 };

                    let userPhotos = this.photos.filter(p => p.photographer_id === this.user.id || p.user_id === this.user.id);
                    
                    let totalLikes = userPhotos.reduce((sum, p) => sum + (p.likes_count || 0), 0);

                    return {
                        posts: userPhotos.length,
                        likes: totalLikes
                    };
                }
            }
        }
    </script>
</body>
</html>