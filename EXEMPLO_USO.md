# Exemplo de Uso - Comida SM

Este documento demonstra como usar a plataforma Comida SM passo a passo.

## 🚀 Iniciando o Sistema

### 1. Inicie o servidor
```bash
composer serve
```

### 2. Acesse o sistema
- **Admin**: http://localhost:8000
- **Cardápio público**: http://lanchonetedoseuze.appzei.com:8000

## 📝 Primeiro Acesso - Criando um Estabelecimento

### 1. Registro
1. Acesse http://localhost:8000/register
2. Preencha os dados:
   - **Nome**: João Silva
   - **Email**: joao@lanchonete.com
   - **Senha**: 123456
   - **Nome do Estabelecimento**: Lanchonete do Seu Zé
   - **Subdomínio**: lanchonetedoseuze

3. Clique em "Criar Conta"

### 2. Dashboard
Após o registro, você será redirecionado para o dashboard com:
- Estatísticas básicas
- Ações rápidas
- Visão geral do estabelecimento

## 🏪 Configurando o Estabelecimento

### 1. Perfil do Estabelecimento
1. Clique em "Perfil" no menu
2. Edite as informações:
   - **Nome**: Lanchonete do Seu Zé
   - **Descrição**: A melhor lanchonete da cidade! Especializada em lanches artesanais e sucos naturais.
   - **Endereço**: Rua das Flores, 123 - Centro
   - **Telefone**: (11) 99999-9999
   - **WhatsApp**: (11) 99999-9999
   - **Instagram**: @lanchonetedoseuze
   - **Tempo de entrega**: 30 minutos
   - **Taxa de entrega**: R$ 5,00
   - **Valor mínimo**: R$ 20,00

3. Configure os horários de funcionamento:
   - **Segunda a Sexta**: 08:00 - 18:00
   - **Sábado**: 08:00 - 16:00
   - **Domingo**: Fechado

4. Faça upload do logo e foto do estabelecimento

## 🍔 Criando Categorias

### 1. Primeira Categoria - Lanches
1. Vá para "Categorias" no menu
2. Clique em "Nova Categoria"
3. Preencha:
   - **Nome**: Lanches
   - **Descrição**: Nossos deliciosos lanches artesanais
   - **Ordem**: 1
4. Faça upload de uma imagem representativa
5. Clique em "Criar Categoria"

### 2. Segunda Categoria - Bebidas
1. Crie outra categoria:
   - **Nome**: Bebidas
   - **Descrição**: Sucos naturais e refrigerantes
   - **Ordem**: 2

### 3. Terceira Categoria - Sobremesas
1. Crie a terceira categoria:
   - **Nome**: Sobremesas
   - **Descrição**: Doces e sobremesas caseiras
   - **Ordem**: 3

## 🍕 Adicionando Produtos

### 1. X-Burger
1. Vá para "Produtos" no menu
2. Clique em "Novo Produto"
3. Preencha:
   - **Nome**: X-Burger
   - **Categoria**: Lanches
   - **Descrição**: Hambúrguer artesanal com carne bovina, queijo, alface, tomate e molho especial
   - **Preço**: R$ 18,90
   - **Ordem**: 1
   - **Disponível**: ✓
4. Adicione opções:
   - **Tamanho**: P (R$ 0,00), M (R$ 3,00), G (R$ 5,00)
   - **Bacon**: R$ 2,00
   - **Ovo**: R$ 1,50
5. Faça upload da imagem
6. Clique em "Criar Produto"

### 2. X-Salada
1. Crie outro produto:
   - **Nome**: X-Salada
   - **Categoria**: Lanches
   - **Descrição**: Hambúrguer vegetariano com queijo, alface, tomate, cebola e molho especial
   - **Preço**: R$ 16,90
   - **Ordem**: 2

### 3. Suco de Laranja
1. Crie um produto de bebida:
   - **Nome**: Suco de Laranja
   - **Categoria**: Bebidas
   - **Descrição**: Suco natural de laranja, sem açúcar
   - **Preço**: R$ 8,90
   - **Ordem**: 1

### 4. Pudim
1. Crie uma sobremesa:
   - **Nome**: Pudim
   - **Categoria**: Sobremesas
   - **Descrição**: Pudim caseiro com calda de caramelo
   - **Preço**: R$ 12,90
   - **Ordem**: 1

## 📱 Testando o Cardápio Público

### 1. Acesse o cardápio
1. Abra uma nova aba no navegador
2. Acesse: http://lanchonetedoseuze.appzei.com:8000
3. Você verá o cardápio público com:
   - Logo e informações do estabelecimento
   - Horários de funcionamento
   - Informações de entrega
   - Categorias e produtos organizados

### 2. Simule um pedido
1. Adicione alguns itens ao carrinho:
   - 1x X-Burger (tamanho M + bacon)
   - 1x Suco de Laranja
   - 1x Pudim
2. Clique no carrinho
3. Verifique os itens e quantidades
4. Clique em "Finalizar Pedido"

### 3. Preencha os dados do cliente
1. **Nome**: Maria Silva
2. **Telefone**: (11) 88888-8888
3. **Endereço**: Rua das Palmeiras, 456 - Vila Nova
4. **Observações**: Sem cebola no lanche
5. Clique em "Confirmar Pedido"

## 📋 Gerenciando Pedidos

### 1. Visualizar pedidos
1. Volte para o painel admin
2. Vá para "Pedidos"
3. Você verá o pedido recém-criado com status "Pendente"

### 2. Atualizar status do pedido
1. Clique no pedido para ver detalhes
2. Altere o status para "Confirmado"
3. Continue atualizando: "Preparando" → "Pronto" → "Entregue"

### 3. Filtrar pedidos
1. Use os filtros por status e data
2. Visualize estatísticas no dashboard

## 🎨 Personalização

### 1. Editar produtos
1. Vá para "Produtos"
2. Clique em "Editar" em qualquer produto
3. Modifique preços, descrições, disponibilidade
4. Adicione ou remova opções

### 2. Reorganizar categorias
1. Vá para "Categorias"
2. Edite a ordem de exibição
3. Modifique descrições e imagens

### 3. Atualizar perfil
1. Vá para "Perfil"
2. Atualize informações de contato
3. Modifique horários de funcionamento
4. Altere configurações de entrega

## 📊 Funcionalidades Avançadas

### 1. Dashboard
- Visualize estatísticas em tempo real
- Acompanhe pedidos do dia
- Monitore produtos mais vendidos

### 2. Gestão de Estoque
- Marque produtos como indisponíveis
- Controle disponibilidade por categoria

### 3. Relatórios
- Histórico de pedidos
- Faturamento por período
- Produtos mais populares

## 🔧 Dicas e Truques

### 1. Organização
- Use nomes descritivos para produtos
- Adicione imagens atrativas
- Organize categorias logicamente

### 2. Preços
- Defina preços competitivos
- Use opções para variações de preço
- Configure valor mínimo adequado

### 3. Atendimento
- Mantenha horários atualizados
- Responda rapidamente aos pedidos
- Use observações para comunicação

### 4. Marketing
- Compartilhe o link do cardápio
- Use redes sociais para divulgação
- Mantenha informações atualizadas

## 🚀 Próximos Passos

1. **Integração com WhatsApp**: Configure notificações automáticas
2. **Pagamentos Online**: Adicione gateways de pagamento
3. **App Mobile**: Desenvolva aplicativo nativo
4. **Analytics**: Implemente relatórios avançados
5. **Marketing**: Crie campanhas promocionais

---

**Parabéns!** Você configurou com sucesso sua plataforma de cardápios digitais! 🎉

Para mais informações, consulte o arquivo `CHECKLIST.md` com todas as funcionalidades planejadas.




