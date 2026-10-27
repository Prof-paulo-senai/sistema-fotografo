<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAEPVision | Mundo SENAI</title>
    
    <!-- Fonte Inter Integrada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Assets Compilados Localmente via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style> 
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F4F6FA] text-slate-800 min-h-screen flex flex-col justify-between" x-data="galleryApp()" x-init="fetchPhotos()">

    <!-- 1. Cabeçalho Limpo Estilo Social / App -->
    <header class="sticky top-0 z-30 bg-[#23429B] text-white shadow-md px-4 sm:px-8 py-3.5">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row justify-between items-center gap-3 md:gap-0">
            
            <!-- Perfil do Usuário Logado -->
            <div class="flex items-center justify-center md:justify-start gap-3 w-full md:w-1/3 order-2 md:order-1">
                <template x-if="user">
                    <div class="flex items-center gap-2.5 bg-[#1A3275]/60 px-3 py-1.5 rounded-full border border-white/10">
                        <img :src="user.avatar ? '/storage/' + user.avatar : '/images/default-avatar.png'" class="w-7 h-7 rounded-full object-cover shrink-0 border border-white">
                        <span class="font-medium text-xs sm:text-sm text-white truncate max-w-[120px]" x-text="'@' + user.name"></span>
                        <button 
                            @click="openProfileModal = true" 
                            :disabled="user.role !== 'fotografo'"
                            class="bg-[#E95378] hover:bg-[#D43F64] text-white px-2.5 py-0.5 text-xs font-semibold rounded-full disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Perfil
                        </button>
                    </div>
                </template>
            </div>

            <!-- Marca Mundo SENAI -->
            <div class="w-full md:w-1/3 text-center order-1 md:order-2 flex items-center justify-center gap-2">
                <span class="bg-[#E95378] text-white font-extrabold px-2 py-0.5 rounded text-xs tracking-wider uppercase">Mundo</span>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                    SAEP<span class="text-slate-200">Vision</span>
                </h1>
            </div>

            <!-- Botões de Acesso -->
            <div class="w-full md:w-1/3 flex justify-center md:justify-end order-3">
                <template x-if="!user">
                    <button @click="showLoginModal = true; authMode = 'login';" class="bg-[#E95378] hover:bg-[#D43F64] text-white px-5 py-2 rounded-lg font-bold shadow transition text-sm">
                        Entrar / Cadastrar
                    </button>
                </template>
                <template x-if="user">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white/10 hover:bg-white/20 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                            Sair
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </header>

    <!-- Sidebar do Perfil do Fotógrafo -->
    <div x-show="openProfileModal" x-cloak class="fixed inset-y-0 left-0 w-full sm:w-80 bg-white shadow-2xl p-6 z-40 border-r border-slate-200">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-800">Estatísticas do Fotógrafo</h2>
            <button @click="openProfileModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
        </div>
        <div class="space-y-3">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total de Curtidas</p>
                <p class="text-2xl font-extrabold text-[#E95378] mt-1" x-text="userStats.likes"></p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Publicações</p>
                <p class="text-2xl font-extrabold text-[#23429B] mt-1" x-text="userStats.posts"></p>
            </div>
        </div>
    </div>

    <!-- 2. Conteúdo Principal da Rede Social -->
    <main class="max-w-5xl w-full mx-auto py-6 px-4 flex-grow">

        <!-- Banner Informativo -->
        <div class="mb-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-[#E95378]"></span>
                    <span class="text-xs font-bold text-[#23429B] uppercase tracking-wider">Curso Técnico em Informática</span>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900">Mostra Interativa de Fotografia</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Vote nas melhores fotos publicadas pelos alunos durante o evento!</p>
            </div>
        </div>

        <!-- Formulário para Fotógrafo publicar -->
        <template x-if="user && user.role === 'fotografo'">
            <div class="mb-8 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm max-w-xl mx-auto">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#23429B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nova Publicação
                </h3>
                <form @submit.prevent="uploadPhoto()" enctype="multipart/form-data" class="space-y-3">
                    <div>
                        <input type="text" x-model="photoForm.title" placeholder="Título da foto..." required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs sm:text-sm focus:outline-none focus:border-[#23429B]">
                    </div>
                    <div>
                        <input type="text" x-model="photoForm.location" placeholder="Localização (ex: Laboratório 02)..." required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs sm:text-sm focus:outline-none focus:border-[#23429B]">
                    </div>
                    <div>
                        <input type="file" @change="photoForm.image = $event.target.files[0]" accept="image/*" required class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#23429B] file:text-white hover:file:bg-[#1A3275] transition">
                    </div>
                    <button type="submit" class="w-full bg-[#23429B] hover:bg-[#1A3275] text-white font-bold py-2 rounded-lg transition text-xs sm:text-sm shadow-sm">
                        Publicar Foto
                    </button>
                </form>
            </div>
        </template>

        <!-- Pesquisa por Fotógrafo -->
        <div class="relative mb-6 max-w-md mx-auto">
            <input 
                type="text" 
                x-model="search" 
                @input.debounce.300ms="fetchPhotos()"
                placeholder="Buscar fotógrafo... ex: @fotografo1" 
                class="w-full bg-white border border-slate-200 rounded-xl py-2.5 px-4 pr-10 text-xs sm:text-sm focus:outline-none focus:border-[#23429B] shadow-sm text-slate-700 placeholder:text-slate-400">
            <svg class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>

        <template x-if="searchError">
            <p class="text-center text-red-500 font-medium mb-6 text-xs" x-text="searchError"></p>
        </template>

        <!-- 2.2 Feed / Galeria estilo Card Social -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
            <template x-for="photo in photos" :key="photo.id">
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                    
                    <!-- Topo do Card -->
                    <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700 truncate" x-text="'@' + photo.photographer"></span>
                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-medium" x-text="photo.location"></span>

                        <template x-if="user && (user.role === 'admin' || Number(user.id) === Number(photo.user_id))">
                            <button 
                                @click="deletePhoto(photo.id)" 
                                title="Excluir foto"
                                class="text-slate-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-colors ml-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </template>
                    </div>

                    <!-- Imagem -->
                    <div class="relative aspect-square bg-slate-100 overflow-hidden">
                        <img :src="photo.image_url" class="w-full h-full object-cover">
                    </div>

                    <!-- Rodapé do Card -->
                    <div class="p-3 flex justify-between items-center bg-white mt-auto">
                        <span class="font-semibold text-slate-800 text-xs sm:text-sm truncate mr-2" x-text="photo.title"></span>
                        
                        <button @click="toggleLike(photo)" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-slate-200 hover:bg-slate-50 transition">
                            <svg class="w-4 h-4 transition-transform active:scale-125" :class="photo.is_liked ? 'text-[#E95378] fill-current' : 'text-slate-400 fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span class="font-bold text-xs text-slate-700" x-text="photo.likes_count"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Modal de Login / Registro -->
    <div x-show="showLoginModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-5 relative border border-slate-100 max-h-[90vh] overflow-y-auto">
            
            <button @click="showLoginModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>

            <!-- Abas -->
            <div class="flex border-b border-slate-100 mb-5">
                <button type="button" @click="authMode = 'login'" :class="authMode === 'login' ? 'border-b-2 border-[#23429B] font-bold text-[#23429B]' : 'text-slate-400'" class="flex-1 py-2 text-center text-xs sm:text-sm">Entrar</button>
                <button type="button" @click="authMode = 'register'" :class="authMode === 'register' ? 'border-b-2 border-[#23429B] font-bold text-[#23429B]' : 'text-slate-400'" class="flex-1 py-2 text-center text-xs sm:text-sm">Cadastrar</button>
            </div>

            <!-- Exibição de Erros Gerais (ex: Credenciais Inválidas) -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs font-semibold text-red-600 mb-1">Atenção:</p>
                    <ul class="list-disc list-inside text-[11px] text-red-500 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulário Login -->
            <form x-show="authMode === 'login'" method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="seu.email@exemplo.com" required class="w-full bg-slate-50 border @error('email') border-red-500 @else border-slate-200 @enderror rounded-lg p-2.5 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('email')
                        <span class="text-[10px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 8 dígitos" minlength="8" required class="w-full bg-slate-50 border @error('password') border-red-500 @else border-slate-200 @enderror rounded-lg p-2.5 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('password')
                        <span class="text-[10px] text-red-500 font-medium mt-0.5 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#23429B] hover:bg-[#1A3275] text-white font-bold py-2.5 rounded-lg text-xs transition shadow-sm">Entrar</button>
                </div>
            </form>

            <!-- Formulário Registro -->
            <form x-show="authMode === 'register'" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-2.5">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: João Silva" required class="w-full bg-slate-50 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('name')
                        <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nome de Usuário</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Ex: joaosilva" required class="w-full bg-slate-50 border @error('username') border-red-500 @else border-slate-200 @enderror rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('username')
                        <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="exemplo@dominio.com" required class="w-full bg-slate-50 border @error('email') border-red-500 @else border-slate-200 @enderror rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('email')
                        <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Tipo de Perfil</label>
                    <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]" required>
                        <option value="usuario" {{ old('role') === 'usuario' ? 'selected' : '' }}>Visitante / Aluno</option>
                        <option value="fotografo" {{ old('role') === 'fotografo' ? 'selected' : '' }}>Fotógrafo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 8 dígitos" minlength="8" required class="w-full bg-slate-50 border @error('password') border-red-500 @else border-slate-200 @enderror rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]">
                    @error('password')
                        <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" placeholder="Mínimo 8 dígitos" minlength="8" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 text-xs focus:outline-none focus:border-[#23429B]">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Foto de Perfil</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-[11px] text-slate-500">
                    @error('avatar')
                        <span class="text-[10px] text-red-500 font-medium block mt-0.5">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#E95378] hover:bg-[#D43F64] text-white font-bold py-2.5 rounded-lg text-xs transition shadow-sm">Criar Conta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Rodapé Institucional Limpo -->
    <footer class="bg-white border-t border-slate-200 text-slate-500 py-4 px-6 mt-12 text-xs">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="bg-[#23429B] text-white px-1.5 py-0.5 rounded text-[10px] font-bold">SENAI</span>
                <span class="font-medium text-slate-700">Mundo SENAI • Curso Técnico em Informática</span>
            </div>
            <span>SENAI</span>
        </div>
    </footer>

    <!-- Lógica Alpine.js -->
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

                    let userPhotos = this.photos.filter(p => Number(p.photographer_id) === Number(this.user.id) || Number(p.user_id) === Number(this.user.id));
                    let totalLikes = userPhotos.reduce((sum, p) => sum + (Number(p.likes_count) || 0), 0);

                    return {
                        posts: userPhotos.length,
                        likes: totalLikes
                    };
                },

                async deletePhoto(id) {
                    if (!confirm('Tem certeza que deseja excluir esta foto?')) return;

                    let res = await fetch(`/photos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (res.ok) {
                        this.fetchPhotos();
                    } else {
                        let data = await res.json();
                        alert(data.message || 'Erro ao excluir a foto.');
                    }
                }
            }
        }
    </script>
</body>
</html>