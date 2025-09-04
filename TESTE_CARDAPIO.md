# Como Testar o Cardápio Localmente

## 1. Configurar o arquivo hosts

### Windows:
1. Abra o **Bloco de Notas como Administrador**
2. Abra o arquivo: `C:\Windows\System32\drivers\etc\hosts`
3. Adicione esta linha no final:
   ```
   127.0.0.1 teste.localhost
   ```
4. Salve o arquivo
5. Reinicie o navegador

### Linux/Mac:
1. Abra o terminal
2. Edite o arquivo hosts:
   ```bash
   sudo nano /etc/hosts
   ```
3. Adicione esta linha:
   ```
   127.0.0.1 teste.localhost
   ```

## 2. Iniciar o servidor

```bash
php -S localhost:8000 -t public
```

## 3. Criar um estabelecimento de teste

1. Acesse: `http://localhost:8000/register`
2. Preencha o formulário:
   - **Nome**: Seu Nome
   - **Email**: seu@email.com
   - **Senha**: 123456
   - **Subdomínio**: `teste` (importante!)
   - **Nome do Estabelecimento**: Minha Lanchonete
3. Clique em "Criar Conta"

## 4. Adicionar produtos e categorias

1. Após o registro, você será redirecionado para o dashboard
2. Vá em **Categorias** e crie algumas categorias (ex: Bebidas, Lanches, Sobremesas)
3. Vá em **Produtos** e adicione alguns produtos em cada categoria

## 5. Testar o cardápio público

### Opção 1: Com arquivo hosts configurado
1. Acesse: `http://teste.localhost:8000`
2. Você verá o cardápio público do seu estabelecimento

### Opção 2: Sem configurar hosts (mais fácil)
1. Acesse: `http://localhost:8000?subdomain=teste`
2. Você verá o cardápio público do seu estabelecimento
3. Teste as funcionalidades:
   - Visualizar produtos
   - Adicionar ao carrinho
   - Fazer pedidos

## 6. URLs disponíveis

### Com arquivo hosts configurado:
- **Admin**: `http://localhost:8000`
- **Cardápio**: `http://teste.localhost:8000`
- **Carrinho**: `http://teste.localhost:8000/cart`
- **Checkout**: `http://teste.localhost:8000/checkout`

### Sem arquivo hosts (usando parâmetro):
- **Admin**: `http://localhost:8000`
- **Cardápio**: `http://localhost:8000?subdomain=teste`
- **Carrinho**: `http://localhost:8000?subdomain=teste&path=/cart`
- **Checkout**: `http://localhost:8000?subdomain=teste&path=/checkout`

## 7. Criar mais estabelecimentos para teste

Para testar com múltiplos estabelecimentos:

1. Adicione mais entradas no arquivo hosts:
   ```
   127.0.0.1 restaurante.localhost
   127.0.0.1 pizzaria.localhost
   127.0.0.1 lanchonete.localhost
   ```

2. Crie contas com subdomínios correspondentes:
   - `restaurante` para `restaurante.localhost`
   - `pizzaria` para `pizzaria.localhost`
   - `lanchonete` para `lanchonete.localhost`

## 8. Solução de problemas

### Se o subdomínio não funcionar:
1. Verifique se o arquivo hosts foi salvo corretamente
2. Reinicie o navegador
3. Limpe o cache do navegador
4. Verifique se o servidor está rodando na porta 8000

### Se der erro 404:
1. Verifique se o subdomínio existe no banco de dados
2. Confirme se o estabelecimento está ativo
3. Verifique os logs do servidor PHP

## 9. Estrutura do banco de dados

O sistema cria automaticamente:
- Tabela `establishments` com o subdomínio
- Tabela `users` com o usuário admin
- Tabela `business_hours` com horários padrão
- Tabelas para `categories`, `products`, `orders`, etc.

## 10. Próximos passos

Após testar localmente, você pode:
1. Configurar um servidor web (Apache/Nginx)
2. Configurar SSL para HTTPS
3. Configurar um domínio real
4. Fazer deploy em produção
