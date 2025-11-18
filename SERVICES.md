# Especificação de Services

Este documento especifica os services implementados no sistema, baseado nos models existentes. Cada service implementa operações CRUD básicas com regras de negócio específicas.

**Status:** ✅ Todos os services listados abaixo foram implementados e estão disponíveis em `App\Services\`.

---

## ⚠️ Observações Importantes

### Localização (Pais, Estado, Cidade)
- **Não foram criados services** para Pais, Estado e Cidade
- **Sempre usar cidade ID 1** nos services que requerem cidade
- Os dados de localização são mantidos apenas via seeders
- A cidade ID 1 é automaticamente atribuída em:
  - `EnderecoService` - ao criar/atualizar endereços
  - `MissaoService` - ao criar/atualizar missões
  - `PontoColetaService` - ao criar/atualizar pontos de coleta

### Categorias e Tipos (Apenas Leitura)
- **CategoriaMissao** e **CategoriaNoticia**: Apenas busca via Model (não há service)
- **TipoItem**: Apenas busca via Model (não há service)
- Estes dados são mantidos apenas via seeders
- Os services que utilizam estes models fazem validação de existência diretamente

---

## 1. Pessoas e Perfis

### 1.1. PerfilService ✅ IMPLEMENTADO

**Model:** `App\Models\Perfil`

**Métodos:**
- `listar()` - Lista todos os perfis
- `buscarPorId(int $id)` - Busca perfil por ID
- `buscarPorNome(string $nome)` - Busca perfil por nome
- `criar(array $dados)` - Cria um novo perfil
- `atualizar(int $id, array $dados)` - Atualiza um perfil existente
- `excluir(int $id)` - Exclui um perfil

**Regras de Negócio:**
- Não permitir exclusão se houver pessoas vinculadas
- Validar nome único e obrigatório
- Descrição opcional

---

### 1.2. PessoaService ✅ IMPLEMENTADO

**Model:** `App\Models\Pessoa`

**Métodos:**
- `listar()` - Lista todas as pessoas (com filtros opcionais)
- `listarAtivas()` - Lista apenas pessoas ativas
- `buscarPorId(int $id)` - Busca pessoa por ID
- `buscarPorCpf(string $cpf)` - Busca pessoa por CPF
- `buscarPorEmail(string $email)` - Busca pessoa por email
- `criar(array $dados)` - Cria uma nova pessoa
- `atualizar(int $id, array $dados)` - Atualiza uma pessoa existente
- `ativar(int $id)` - Ativa uma pessoa
- `desativar(int $id)` - Desativa uma pessoa
- `excluir(int $id)` - Exclui uma pessoa (soft delete recomendado)

**Regras de Negócio:**
- Validar que perfil_id existe
- Validar CPF único (se informado)
- Validar email único (se informado)
- Validar formato de CPF (se informado)
- Validar formato de email (se informado)
- Validar data de nascimento não pode ser futura
- Campo ativo padrão: true
- dt_cadastro e dt_atualizacao preenchidos automaticamente
- Não permitir exclusão se houver doações, missões criadas, notícias criadas ou pontos de coleta criados vinculados

---

### 1.3. EnderecoService ✅ IMPLEMENTADO

**Model:** `App\Models\Endereco`

**Métodos:**
- `listarPorPessoa(int $pessoaId)` - Lista endereços de uma pessoa
- `buscarPorId(int $id)` - Busca endereço por ID
- `buscarPrincipal(int $pessoaId)` - Busca endereço principal de uma pessoa
- `criar(array $dados)` - Cria um novo endereço
- `atualizar(int $id, array $dados)` - Atualiza um endereço existente
- `definirComoPrincipal(int $id)` - Define um endereço como principal
- `excluir(int $id)` - Exclui um endereço

**Regras de Negócio Implementadas:**
- ✅ Validar que pessoa_id existe
- ✅ **Sempre usa cidade ID 1** (atribuído automaticamente)
- ✅ Se definir como principal, desmarcar outros endereços principais da mesma pessoa
- ✅ Permitir múltiplos endereços por pessoa
- ✅ Campos obrigatórios: pessoa_id, cep, logradouro, numero, bairro (cidades_id é automático)

---

### 1.4. VoluntarioService ✅ IMPLEMENTADO

**Model:** `App\Models\Voluntario`

**Métodos:**
- `listar()` - Lista todos os voluntários
- `listarPorStatus(string $status)` - Lista voluntários por status
- `listarAprovados()` - Lista apenas voluntários aprovados
- `buscarPorId(int $id)` - Busca voluntário por ID
- `buscarPorPessoa(int $pessoaId)` - Busca voluntário por pessoa
- `criar(array $dados)` - Cria um novo voluntário
- `atualizar(int $id, array $dados)` - Atualiza um voluntário existente
- `aprovar(int $id)` - Aprova um voluntário
- `rejeitar(int $id, string $obs)` - Rejeita um voluntário
- `excluir(int $id)` - Exclui um voluntário

**Regras de Negócio:**
- Validar que pessoa_id existe e é única (uma pessoa só pode ser um voluntário)
- Validar que pessoa_id não está vinculada a outro voluntário
- Status padrão ao criar: pendente
- Ao aprovar, preencher dt_aprovacao automaticamente
- Não permitir exclusão se houver candidaturas vinculadas
- Campos opcionais: escolaridade, profissao, habilidades, disponibilidade, exp_emergencias, cnh_categoria, obs

---

## 2. Missões

### 2.1. CategoriaMissao

**Model:** `App\Models\CategoriaMissao`

**⚠️ Não há service implementado** - Apenas busca direta via Model
- Os dados são mantidos apenas via seeders
- O `MissaoService` valida a existência da categoria diretamente

---

### 2.2. MissaoService ✅ IMPLEMENTADO

**Model:** `App\Models\Missao`

**Métodos:**
- `listar()` - Lista todas as missões (com filtros opcionais)
- `listarPorStatus(string $status)` - Lista missões por status
- `listarPorCategoria(int $categoriaId)` - Lista missões por categoria
- `listarPorCidade(int $cidadeId)` - Lista missões por cidade
- `listarDisponiveis()` - Lista missões com vagas disponíveis
- `buscarPorId(int $id)` - Busca missão por ID
- `criar(array $dados, int $adminId)` - Cria uma nova missão
- `atualizar(int $id, array $dados)` - Atualiza uma missão existente
- `atualizarVagas(int $id, int $vagasPreenchidas)` - Atualiza vagas preenchidas
- `finalizar(int $id)` - Finaliza uma missão
- `cancelar(int $id)` - Cancela uma missão
- `excluir(int $id)` - Exclui uma missão

**Regras de Negócio Implementadas:**
- ✅ Validar que categoria_id existe
- ✅ **Sempre usa cidade ID 1** (atribuído automaticamente)
- ✅ Validar que admin_criador_id existe e tem perfil ADMIN
- ✅ Validar dt_inicio não pode ser anterior à data atual
- ✅ Validar dt_fim não pode ser anterior a dt_inicio
- ✅ Validar vagas_totais > 0
- ✅ vagas_preenchidas padrão: 0
- ✅ Não permitir vagas_preenchidas > vagas_totais
- ✅ Status padrão ao criar: 'ativa'
- ✅ dt_criacao e dt_atualizacao preenchidos automaticamente
- ✅ Ao aprovar candidatura, verificar se há vagas disponíveis e incrementar vagas_preenchidas
- ✅ Não permitir exclusão se houver candidaturas vinculadas

---

### 2.3. CandidaturaMissaoService ✅ IMPLEMENTADO

**Model:** `App\Models\CandidaturaMissao`

**Métodos:**
- `listar()` - Lista todas as candidaturas
- `listarPorMissao(int $missaoId)` - Lista candidaturas de uma missão
- `listarPorVoluntario(int $voluntarioId)` - Lista candidaturas de um voluntário
- `listarPorStatus(string $status)` - Lista candidaturas por status
- `buscarPorId(int $id)` - Busca candidatura por ID
- `criar(array $dados)` - Cria uma nova candidatura
- `atualizar(int $id, array $dados)` - Atualiza uma candidatura existente
- `aprovar(int $id)` - Aprova uma candidatura
- `rejeitar(int $id, string $obs)` - Rejeita uma candidatura
- `concluir(int $id, int $avaliacao, string $obsAvaliacao)` - Conclui uma candidatura com avaliação
- `excluir(int $id)` - Exclui uma candidatura

**Regras de Negócio Implementadas:**
- ✅ Validar que missao_id existe
- ✅ Validar que voluntario_id existe e é um voluntário aprovado
- ✅ Validar que voluntário não está duplicado na mesma missão
- ✅ Validar que missão tem vagas disponíveis antes de aprovar
- ✅ Status padrão ao criar: 'pendente'
- ✅ dt_candidatura preenchido automaticamente
- ✅ Ao aprovar: preencher dt_aprovacao e incrementar vagas_preenchidas da missão automaticamente
- ✅ Ao rejeitar: não incrementar vagas
- ✅ Ao concluir: preencher dt_conclusao e permitir avaliação (1-5)
- ✅ Validar avaliação entre 1 e 5 (se informada)
- ✅ Ao excluir candidatura aprovada, decrementar vagas da missão

---

## 3. Notícias

### 3.1. CategoriaNoticia

**Model:** `App\Models\CategoriaNoticia`

**⚠️ Não há service implementado** - Apenas busca direta via Model
- Os dados são mantidos apenas via seeders
- O `NoticiaService` valida a existência da categoria diretamente

---

### 3.2. NoticiaService ✅ IMPLEMENTADO

**Model:** `App\Models\Noticia`

**Métodos:**
- `listar()` - Lista todas as notícias (com filtros opcionais)
- `listarPublicadas()` - Lista apenas notícias publicadas
- `listarDestaque()` - Lista notícias em destaque
- `listarPorCategoria(int $categoriaId)` - Lista notícias por categoria
- `listarPorStatus(string $status)` - Lista notícias por status
- `buscarPorId(int $id)` - Busca notícia por ID
- `criar(array $dados, int $adminId)` - Cria uma nova notícia
- `atualizar(int $id, array $dados)` - Atualiza uma notícia existente
- `publicar(int $id)` - Publica uma notícia
- `definirDestaque(int $id, bool $destaque)` - Define notícia como destaque
- `incrementarVisualizacoes(int $id)` - Incrementa contador de visualizações
- `excluir(int $id)` - Exclui uma notícia

**Regras de Negócio Implementadas:**
- ✅ Validar que categoria_id existe
- ✅ Validar que admin_autor_id existe e tem perfil ADMIN
- ✅ Validar título obrigatório
- ✅ Validar conteúdo obrigatório
- ✅ Status padrão ao criar: 'rascunho'
- ✅ destaque padrão: false
- ✅ visualizacoes padrão: 0
- ✅ dt_publicacao preenchido ao publicar
- ✅ dt_atualizacao atualizado automaticamente
- ✅ Não permitir exclusão se houver imagens vinculadas

---

### 3.3. ImagemNoticiaService ✅ IMPLEMENTADO

**Model:** `App\Models\ImagemNoticia`

**Métodos:**
- `listarPorNoticia(int $noticiaId)` - Lista imagens de uma notícia
- `buscarPorId(int $id)` - Busca imagem por ID
- `buscarPrincipal(int $noticiaId)` - Busca imagem principal de uma notícia
- `criar(array $dados)` - Cria uma nova imagem
- `atualizar(int $id, array $dados)` - Atualiza uma imagem existente
- `definirComoPrincipal(int $id)` - Define uma imagem como principal
- `reordenar(int $noticiaId, array $ordens)` - Reordena imagens de uma notícia
- `excluir(int $id)` - Exclui uma imagem

**Regras de Negócio Implementadas:**
- ✅ Validar que noticia_id existe
- ✅ Validar URL obrigatória
- ✅ Se definir como principal, desmarcar outras imagens principais da mesma notícia
- ✅ ordem padrão: próximo número disponível (calculado automaticamente)
- ✅ dt_upload preenchido automaticamente
- ✅ Legenda opcional

---

## 4. Doações

### 4.1. TipoItem

**Model:** `App\Models\TipoItem`

**⚠️ Não há service implementado** - Apenas busca direta via Model
- Os dados são mantidos apenas via seeders
- Os services `NecessidadePontoService`, `ItemDoacaoService` e `DoacaoService` validam a existência do tipo de item diretamente

---

### 4.2. PontoColetaService ✅ IMPLEMENTADO

**Model:** `App\Models\PontoColeta`

**Métodos:**
- `listar()` - Lista todos os pontos de coleta
- `listarAtivos()` - Lista apenas pontos de coleta ativos
- `listarPorCidade(int $cidadeId)` - Lista pontos por cidade
- `buscarPorId(int $id)` - Busca ponto de coleta por ID
- `criar(array $dados, int $adminId)` - Cria um novo ponto de coleta
- `atualizar(int $id, array $dados)` - Atualiza um ponto de coleta existente
- `ativar(int $id)` - Ativa um ponto de coleta
- `desativar(int $id)` - Desativa um ponto de coleta
- `excluir(int $id)` - Exclui um ponto de coleta

**Regras de Negócio Implementadas:**
- ✅ **Sempre usa cidade ID 1** (atribuído automaticamente)
- ✅ Validar que admin_criador_id existe e tem perfil ADMIN
- ✅ Validar nome obrigatório
- ✅ ativo padrão: true
- ✅ dt_criacao preenchido automaticamente
- ✅ Não permitir exclusão se houver necessidades ou doações vinculadas

---

### 4.3. NecessidadePontoService ✅ IMPLEMENTADO

**Model:** `App\Models\NecessidadePonto`

**Métodos:**
- `listar()` - Lista todas as necessidades
- `listarPorPonto(int $pontoColetaId)` - Lista necessidades de um ponto
- `listarAtivas()` - Lista apenas necessidades ativas
- `listarPorPrioridade(string $prioridade)` - Lista necessidades por prioridade
- `buscarPorId(int $id)` - Busca necessidade por ID
- `criar(array $dados)` - Cria uma nova necessidade
- `atualizar(int $id, array $dados)` - Atualiza uma necessidade existente
- `atualizarQuantidadeRecebida(int $id, float $quantidade)` - Atualiza quantidade recebida
- `ativar(int $id)` - Ativa uma necessidade
- `desativar(int $id)` - Desativa uma necessidade
- `excluir(int $id)` - Exclui uma necessidade

**Regras de Negócio Implementadas:**
- ✅ Validar que ponto_coleta_id existe
- ✅ Validar que tipo_item_id existe
- ✅ Validar quantidade_necessaria > 0
- ✅ quantidade_recebida padrão: 0
- ✅ Não permitir quantidade_recebida > quantidade_necessaria
- ✅ prioridade padrão: 'media'
- ✅ ativo padrão: true
- ✅ dt_criacao e dt_atualizacao preenchidos automaticamente
- ✅ Ao registrar entrega de doação, atualizar quantidade_recebida automaticamente

---

### 4.4. DoacaoService ✅ IMPLEMENTADO

**Model:** `App\Models\Doacao`

**Métodos:**
- `listar()` - Lista todas as doações
- `listarPorPessoa(int $pessoaId)` - Lista doações de uma pessoa
- `listarPorPonto(int $pontoColetaId)` - Lista doações de um ponto
- `listarPorStatus(string $status)` - Lista doações por status
- `buscarPorId(int $id)` - Busca doação por ID
- `criar(array $dados, array $itens)` - Cria uma nova doação com itens
- `atualizar(int $id, array $dados)` - Atualiza uma doação existente
- `registrarEntrega(int $id)` - Registra entrega da doação
- `cancelar(int $id)` - Cancela uma doação
- `excluir(int $id)` - Exclui uma doação

**Regras de Negócio Implementadas:**
- ✅ Validar que pessoa_id existe
- ✅ Validar que ponto_coleta_id existe e está ativo
- ✅ Validar que há pelo menos um item na doação
- ✅ dt_doacao preenchido automaticamente
- ✅ Status padrão ao criar: 'pendente'
- ✅ Ao registrar entrega: preencher dt_entrega e atualizar quantidade_recebida das necessidades automaticamente (via transação)
- ✅ Não permitir exclusão se já foi entregue
- ✅ Não permitir cancelar se já foi entregue
- ✅ Criação de doação com múltiplos itens em transação

---

### 4.5. ItemDoacaoService ✅ IMPLEMENTADO

**Model:** `App\Models\ItemDoacao`

**Métodos:**
- `listarPorDoacao(int $doacaoId)` - Lista itens de uma doação
- `buscarPorId(int $id)` - Busca item por ID
- `criar(array $dados)` - Cria um novo item de doação
- `atualizar(int $id, array $dados)` - Atualiza um item de doação existente
- `excluir(int $id)` - Exclui um item de doação

**Regras de Negócio Implementadas:**
- ✅ Validar que doacao_id existe
- ✅ Validar que tipo_item_id existe
- ✅ Validar quantidade > 0
- ✅ Observação opcional
- ✅ Não permitir exclusão/atualização se doação já foi entregue

---

## Observações Gerais

### ✅ Validações Implementadas
- ✅ Todos os IDs são validados antes de uso
- ✅ Campos obrigatórios são validados
- ✅ Relacionamentos são respeitados antes de exclusões
- ✅ Exceções são lançadas com mensagens claras (`ModelNotFoundException`, `InvalidArgumentException`, `RuntimeException`)

### ✅ Regras de Permissão Implementadas
- ✅ Apenas pessoas com perfil ADMIN podem criar:
  - Missões (validação em `MissaoService::criar()`)
  - Notícias (validação em `NoticiaService::criar()`)
  - Pontos de Coleta (validação em `PontoColetaService::criar()`)
- ✅ Voluntários devem estar aprovados para:
  - Candidatar-se a missões (validação em `CandidaturaMissaoService::criar()`)

### ⚠️ Regras Especiais Implementadas
- ✅ **Cidade sempre ID 1**: Aplicado automaticamente em:
  - `EnderecoService` - ao criar/atualizar endereços
  - `MissaoService` - ao criar/atualizar missões
  - `PontoColetaService` - ao criar/atualizar pontos de coleta
- ✅ **Categorias e Tipos apenas leitura**: 
  - `CategoriaMissao` e `CategoriaNoticia` - apenas busca via Model
  - `TipoItem` - apenas busca via Model
  - Dados mantidos apenas via seeders

### ✅ Auditoria Implementada
- ✅ dt_cadastro e dt_atualizacao preenchidos automaticamente em `PessoaService`
- ✅ dt_criacao preenchido automaticamente em `MissaoService` e `PontoColetaService`
- ✅ dt_atualizacao atualizado automaticamente em todos os services que atualizam registros
- ✅ dt_aprovacao preenchido ao aprovar voluntário ou candidatura
- ✅ dt_publicacao preenchido ao publicar notícia
- ✅ dt_upload preenchido ao criar imagem
- ✅ dt_doacao e dt_entrega preenchidos automaticamente em `DoacaoService`

### 📝 Services Implementados (12)
1. ✅ PerfilService
2. ✅ PessoaService
3. ✅ EnderecoService
4. ✅ VoluntarioService
5. ✅ MissaoService
6. ✅ CandidaturaMissaoService
7. ✅ NoticiaService
8. ✅ ImagemNoticiaService
9. ✅ PontoColetaService
10. ✅ NecessidadePontoService
11. ✅ DoacaoService
12. ✅ ItemDoacaoService

### 📝 Services NÃO Implementados (Apenas Leitura)
- PaisService - usar sempre ID 1 via seeder
- EstadoService - usar sempre ID 1 via seeder
- CidadeService - usar sempre ID 1 via seeder
- CategoriaMissaoService - apenas busca via Model
- CategoriaNoticiaService - apenas busca via Model
- TipoItemService - apenas busca via Model
