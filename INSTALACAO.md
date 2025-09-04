# 🚀 Instalação do Comida SM

## Pré-requisitos

- **XAMPP** (Apache + MySQL + PHP 8.2+)
- **MySQL** habilitado no XAMPP
- **mod_rewrite** habilitado no Apache

## 📋 Passo a Passo

### 1. Preparar o XAMPP

1. Abra o **XAMPP Control Panel**
2. Inicie o **Apache** e **MySQL**
3. Acesse `http://localhost/phpmyadmin` para verificar se o MySQL está funcionando

### 2. Instalar a Aplicação

1. Acesse `http://localhost/install.php`
2. Preencha os dados do banco de dados:
   - **Host:** localhost
   - **Porta:** 3306
   - **Nome do Banco:** comida_sm (ou o que preferir)
   - **Usuário:** root
   - **Senha:** (deixe vazio se não tiver senha)
3. Configure os dados da aplicação e usuário administrador
4. Clique em "Instalar Aplicação"

### 3. Finalizar Instalação

1. Após a instalação bem-sucedida, acesse `http://localhost/`
2. Faça login com as credenciais do administrador criadas
3. **IMPORTANTE:** Execute `http://localhost/remove-installer.php` para remover os arquivos de instalação por segurança

## 🔧 Configuração Manual (Alternativa)

Se preferir configurar manualmente:

1. Crie um banco de dados MySQL chamado `comida_sm`
2. Copie o conteúdo do arquivo `.env.example` para `.env`
3. Configure as variáveis de ambiente no arquivo `.env`
4. Execute o script SQL em `database/schema.sql` no seu banco

## 📁 Estrutura de Arquivos

```
comida_sm/
├── install.php              # Instalador web
├── remove-installer.php     # Remove instalador após instalação
├── .env                     # Configurações (criado automaticamente)
├── database/
│   ├── schema.sql          # Script SQL para MySQL
│   └── installed.flag      # Flag de instalação
├── public/                 # Arquivos públicos
├── src/                    # Código fonte
└── templates/              # Templates PHP
```

## 🚨 Segurança

- **SEMPRE** remova o arquivo `install.php` após a instalação
- Altere a senha padrão do administrador
- Configure corretamente as permissões de arquivo
- Use HTTPS em produção

## 🆘 Solução de Problemas

### Erro de Conexão com Banco
- Verifique se o MySQL está rodando no XAMPP
- Confirme as credenciais no arquivo `.env`
- Teste a conexão no phpMyAdmin

### CSS não Carrega
- Verifique se o mod_rewrite está habilitado
- Confirme se o arquivo `.htaccess` está presente
- Teste acessando `http://localhost/css/output.css` diretamente

### Página em Branco
- Verifique os logs de erro do PHP
- Confirme se todas as dependências estão instaladas
- Execute `composer install` se necessário

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs de erro do Apache/PHP
2. Confirme se todos os pré-requisitos estão atendidos
3. Teste com um banco de dados limpo

---

**Comida SM** - Sistema de Delivery para Estabelecimentos
