<?php
$title = 'Cadastro - Comida SM';
$showNavbar = false;

?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Crie sua conta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="/login" class="font-medium text-primary-600 hover:text-primary-500">
                    faça login
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                           placeholder="Seu nome completo">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                           placeholder="seu@email.com">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                           placeholder="Mínimo 6 caracteres">
                </div>
                
                <div>
                    <label for="establishment_name" class="block text-sm font-medium text-gray-700">Nome do Estabelecimento</label>
                    <input id="establishment_name" name="establishment_name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                           placeholder="Ex: Lanchonete do Seu Zé">
                </div>
                
                <div>
                    <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdomínio</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input id="subdomain" name="subdomain" type="text" required 
                               class="flex-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-l-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                               placeholder="lanchonetedoseuze">
                        <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">
                            .appzei.com
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Seu cardápio ficará disponível em: lanchonetedoseuze.appzei.com</p>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Criar Conta
                </button>
            </div>
        </form>
    </div>
</div>

<?php


?>

