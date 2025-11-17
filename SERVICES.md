# 📋 Services da Aplicação

Este documento lista todas as services que devem ser criadas para gerenciar as models da aplicação, organizadas por domínio de negócio.

---

## 📍 **1. Localização Services**

### **1.1 PaisService**
**Responsabilidade:** Gerenciar países do sistema

**Funcionalidades:**
- `listar()` - Listar todos os países com paginação
- `buscarPorId(int $id)` - Buscar país por ID
- `buscarPorSigla(string $sigla)` - Buscar país por sigla
- `criar(array $dados)` - Criar novo país
- `atualizar(int $id, array $dados)` - Atualizar país existente
- `excluir(int $id)` - Excluir país (com validação de estados relacionados)
- `listarComEstados()` - Listar países com seus estados relacionados

---

### **1.2 EstadoService**
**Responsabilidade:** Gerenciar estados do sistema

**Funcionalidades:**
- `listar()` - Listar todos os estados com paginação
- `buscarPorId(int $id)` - Buscar estado por ID
- `buscarPorUf(string $uf)` - Buscar estado por UF
- `buscarPorPais(int $paisId)` - Listar estados de um país
- `criar(array $dados)` - Criar novo estado
- `atualizar(int $id, array $dados)` - Atualizar estado existente
- `excluir(int $id)` - Excluir estado (com validação de cidades relacionadas)
- `listarComCidades(int $id)` - Buscar estado com suas cidades

---

### **1.3 CidadeService**
**Responsabilidade:** Gerenciar cidades do sistema

**Funcionalidades:**
- `listar()` - Listar todas as cidades com paginação
- `buscarPorId(int $id)` - Buscar cidade por ID
- `buscarPorEstado(int $estadoId)` - Listar cidades de um estado
- `buscarPorCodIbge(string $codIbge)` - Buscar cidade por código IBGE
- `buscarPorNome(string $nome)` - Buscar cidades por nome (busca parcial)
- `criar(array $dados)` - Criar nova cidade
- `atualizar(int $id, array $dados)` - Atualizar cidade existente
- `excluir(int $id)` - Excluir cidade (com validação de endereços relacionados)
- `listarComEstado(int $id)` - Buscar cidade com seu estado

---

## 👥 **2. Pessoas e Perfis Services**

### **2.1 PerfilService**
**Responsabilidade:** Gerenciar perfis de usuários

**Funcionalidades:**
- `listar()` - Listar todos os perfis
- `buscarPorId(int $id)` - Buscar perfil por ID
- `buscarPorNome(string $nome)` - Buscar perfil por nome
- `criar(array $dados)` - Criar novo perfil
- `atualizar(int $id, array $dados)` - Atualizar perfil existente
- `excluir(int $id)` - Excluir perfil (com validação de pessoas relacionadas)
- `listarComPessoas(int $id)` - Buscar perfil com pessoas relacionadas

---

### **2.2 PessoaService**
**Responsabilidade:** Gerenciar pessoas do sistema

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar pessoas com filtros e paginação
- `buscarPorId(int $id)` - Buscar pessoa por ID com relacionamentos
- `buscarPorCpf(string $cpf)` - Buscar pessoa por CPF
- `buscarPorEmail(string $email)` - Buscar pessoa por email
- `criar(array $dados)` - Criar nova pessoa
- `atualizar(int $id, array $dados)` - Atualizar pessoa existente
- `ativar(int $id)` - Ativar pessoa
- `desativar(int $id)` - Desativar pessoa
- `excluir(int $id)` - Excluir pessoa (soft delete ou validação)
- `validarCpf(string $cpf, ?int $excluirId = null)` - Validar CPF único
- `validarEmail(string $email, ?int $excluirId = null)` - Validar email único
- `listarComRelacionamentos(int $id)` - Buscar pessoa com todos os relacionamentos

---

### **2.3 EnderecoService**
**Responsabilidade:** Gerenciar endereços de pessoas

**Funcionalidades:**
- `listarPorPessoa(int $pessoaId)` - Listar endereços de uma pessoa
- `buscarPorId(int $id)` - Buscar endereço por ID
- `buscarPrincipal(int $pessoaId)` - Buscar endereço principal de uma pessoa
- `criar(array $dados)` - Criar novo endereço
- `atualizar(int $id, array $dados)` - Atualizar endereço existente
- `definirComoPrincipal(int $id)` - Definir endereço como principal (remove principal de outros)
- `excluir(int $id)` - Excluir endereço
- `validarCep(string $cep)` - Validar formato de CEP

---

### **2.4 VoluntarioService**
**Responsabilidade:** Gerenciar voluntários do sistema

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar voluntários com filtros (status, cidade, etc.)
- `buscarPorId(int $id)` - Buscar voluntário por ID com relacionamentos
- `buscarPorPessoa(int $pessoaId)` - Buscar voluntário por pessoa
- `criar(array $dados)` - Criar novo voluntário (cria pessoa se necessário)
- `atualizar(int $id, array $dados)` - Atualizar voluntário existente
- `aprovar(int $id, ?string $obs = null)` - Aprovar voluntário
- `rejeitar(int $id, string $obs)` - Rejeitar voluntário
- `alterarStatus(int $id, string $status)` - Alterar status do voluntário
- `listarAprovados()` - Listar apenas voluntários aprovados
- `listarPendentes()` - Listar voluntários pendentes de aprovação
- `excluir(int $id)` - Excluir voluntário

---

## 🎯 **3. Missões Services**

### **3.1 CategoriaMissaoService**
**Responsabilidade:** Gerenciar categorias de missões

**Funcionalidades:**
- `listar()` - Listar todas as categorias
- `buscarPorId(int $id)` - Buscar categoria por ID
- `buscarPorNome(string $nome)` - Buscar categoria por nome
- `criar(array $dados)` - Criar nova categoria
- `atualizar(int $id, array $dados)` - Atualizar categoria existente
- `excluir(int $id)` - Excluir categoria (com validação de missões relacionadas)
- `listarComMissoes(int $id)` - Buscar categoria com missões relacionadas

---

### **3.2 MissaoService**
**Responsabilidade:** Gerenciar missões do sistema

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar missões com filtros (status, categoria, cidade, data)
- `buscarPorId(int $id)` - Buscar missão por ID com relacionamentos
- `criar(array $dados, int $adminId)` - Criar nova missão
- `atualizar(int $id, array $dados)` - Atualizar missão existente
- `cancelar(int $id, ?string $motivo = null)` - Cancelar missão
- `finalizar(int $id)` - Finalizar missão
- `listarAbertas()` - Listar missões com status ABERTA
- `listarPorCategoria(int $categoriaId)` - Listar missões por categoria
- `listarPorCidade(int $cidadeId)` - Listar missões por cidade
- `listarProximas()` - Listar missões com data de início futura
- `verificarVagas(int $id)` - Verificar disponibilidade de vagas
- `incrementarVagasPreenchidas(int $id)` - Incrementar vagas preenchidas
- `decrementarVagasPreenchidas(int $id)` - Decrementar vagas preenchidas
- `excluir(int $id)` - Excluir missão (com validação de candidaturas)

---

### **3.3 CandidaturaMissaoService**
**Responsabilidade:** Gerenciar candidaturas de voluntários para missões

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar candidaturas com filtros
- `buscarPorId(int $id)` - Buscar candidatura por ID
- `criar(int $missaoId, int $voluntarioId)` - Criar nova candidatura
- `aprovar(int $id, ?string $obs = null)` - Aprovar candidatura
- `rejeitar(int $id, string $obs)` - Rejeitar candidatura
- `concluir(int $id, ?int $avaliacao = null, ?string $obs = null)` - Concluir candidatura com avaliação
- `cancelar(int $id, ?string $motivo = null)` - Cancelar candidatura
- `listarPorMissao(int $missaoId)` - Listar candidaturas de uma missão
- `listarPorVoluntario(int $voluntarioId)` - Listar candidaturas de um voluntário
- `listarAprovadas(int $missaoId)` - Listar candidaturas aprovadas de uma missão
- `listarPendentes(int $missaoId)` - Listar candidaturas pendentes de uma missão
- `verificarCandidaturaExistente(int $missaoId, int $voluntarioId)` - Verificar se já existe candidatura
- `excluir(int $id)` - Excluir candidatura

---

## 📰 **4. Notícias Services**

### **4.1 CategoriaNoticiaService**
**Responsabilidade:** Gerenciar categorias de notícias

**Funcionalidades:**
- `listar()` - Listar todas as categorias
- `buscarPorId(int $id)` - Buscar categoria por ID
- `buscarPorNome(string $nome)` - Buscar categoria por nome
- `criar(array $dados)` - Criar nova categoria
- `atualizar(int $id, array $dados)` - Atualizar categoria existente
- `excluir(int $id)` - Excluir categoria (com validação de notícias relacionadas)
- `listarComNoticias(int $id)` - Buscar categoria com notícias relacionadas

---

### **4.2 NoticiaService**
**Responsabilidade:** Gerenciar notícias do sistema

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar notícias com filtros (status, categoria, destaque)
- `buscarPorId(int $id)` - Buscar notícia por ID com relacionamentos
- `buscarPublica(int $id)` - Buscar notícia pública (incrementa visualizações)
- `criar(array $dados, int $adminId)` - Criar nova notícia
- `atualizar(int $id, array $dados)` - Atualizar notícia existente
- `publicar(int $id)` - Publicar notícia
- `arquivar(int $id)` - Arquivar notícia
- `definirDestaque(int $id, bool $destaque)` - Definir/remover destaque
- `listarPublicadas()` - Listar apenas notícias publicadas
- `listarDestaques()` - Listar notícias em destaque
- `listarPorCategoria(int $categoriaId)` - Listar notícias por categoria
- `incrementarVisualizacoes(int $id)` - Incrementar contador de visualizações
- `excluir(int $id)` - Excluir notícia

---

### **4.3 ImagemNoticiaService**
**Responsabilidade:** Gerenciar imagens de notícias

**Funcionalidades:**
- `listarPorNoticia(int $noticiaId)` - Listar imagens de uma notícia
- `buscarPorId(int $id)` - Buscar imagem por ID
- `buscarPrincipal(int $noticiaId)` - Buscar imagem principal de uma notícia
- `criar(array $dados)` - Criar nova imagem (upload + registro)
- `atualizar(int $id, array $dados)` - Atualizar imagem existente
- `definirComoPrincipal(int $id)` - Definir imagem como principal
- `reordenar(int $noticiaId, array $ordemIds)` - Reordenar imagens
- `excluir(int $id)` - Excluir imagem (remove arquivo físico)
- `excluirPorNoticia(int $noticiaId)` - Excluir todas as imagens de uma notícia

---

## 🎁 **5. Doações Services**

### **5.1 TipoItemService**
**Responsabilidade:** Gerenciar tipos de itens para doações

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar tipos de itens com filtros
- `buscarPorId(int $id)` - Buscar tipo de item por ID
- `buscarPorNome(string $nome)` - Buscar tipo de item por nome
- `buscarPorCategoria(string $categoria)` - Listar tipos de itens por categoria
- `criar(array $dados)` - Criar novo tipo de item
- `atualizar(int $id, array $dados)` - Atualizar tipo de item existente
- `excluir(int $id)` - Excluir tipo de item (com validação de necessidades e itens relacionados)
- `listarComNecessidades(int $id)` - Buscar tipo de item com necessidades relacionadas

---

### **5.2 PontoColetaService**
**Responsabilidade:** Gerenciar pontos de coleta

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar pontos de coleta com filtros (cidade, ativo)
- `buscarPorId(int $id)` - Buscar ponto de coleta por ID com relacionamentos
- `buscarPorCidade(int $cidadeId)` - Listar pontos de coleta de uma cidade
- `buscarProximos(float $latitude, float $longitude, float $raioKm = 10)` - Buscar pontos próximos por geolocalização
- `criar(array $dados, int $adminId)` - Criar novo ponto de coleta
- `atualizar(int $id, array $dados)` - Atualizar ponto de coleta existente
- `ativar(int $id)` - Ativar ponto de coleta
- `desativar(int $id)` - Desativar ponto de coleta
- `listarAtivos()` - Listar apenas pontos ativos
- `listarComNecessidades(int $id)` - Buscar ponto com necessidades relacionadas
- `listarComDoacoes(int $id)` - Buscar ponto com doações relacionadas
- `excluir(int $id)` - Excluir ponto de coleta (com validação de doações)

---

### **5.3 NecessidadePontoService**
**Responsabilidade:** Gerenciar necessidades dos pontos de coleta

**Funcionalidades:**
- `listarPorPonto(int $pontoId)` - Listar necessidades de um ponto de coleta
- `buscarPorId(int $id)` - Buscar necessidade por ID
- `buscarPorPontoETipo(int $pontoId, int $tipoItemId)` - Buscar necessidade específica
- `criar(array $dados)` - Criar nova necessidade
- `atualizar(int $id, array $dados)` - Atualizar necessidade existente
- `atualizarQuantidadeRecebida(int $id, float $quantidade)` - Atualizar quantidade recebida
- `incrementarQuantidadeRecebida(int $id, float $quantidade)` - Incrementar quantidade recebida
- `alterarPrioridade(int $id, string $prioridade)` - Alterar prioridade
- `ativar(int $id)` - Ativar necessidade
- `desativar(int $id)` - Desativar necessidade
- `verificarNecessidadeAtendida(int $id)` - Verificar se necessidade foi totalmente atendida
- `listarAtivas(int $pontoId)` - Listar necessidades ativas de um ponto
- `listarPorPrioridade(int $pontoId, string $prioridade)` - Listar necessidades por prioridade
- `excluir(int $id)` - Excluir necessidade

---

### **5.4 DoacaoService**
**Responsabilidade:** Gerenciar doações

**Funcionalidades:**
- `listar(array $filtros = [])` - Listar doações com filtros (pessoa, ponto, status, data)
- `buscarPorId(int $id)` - Buscar doação por ID com relacionamentos
- `listarPorPessoa(int $pessoaId)` - Listar doações de uma pessoa
- `listarPorPonto(int $pontoId)` - Listar doações de um ponto de coleta
- `criar(array $dados, array $itens)` - Criar nova doação com itens
- `atualizar(int $id, array $dados)` - Atualizar doação existente
- `registrarEntrega(int $id, ?string $obs = null)` - Registrar entrega da doação
- `cancelar(int $id, string $motivo)` - Cancelar doação
- `alterarStatus(int $id, string $status)` - Alterar status da doação
- `listarPorStatus(string $status)` - Listar doações por status
- `listarPendentes(int $pontoId)` - Listar doações pendentes de um ponto
- `adicionarItem(int $doacaoId, array $item)` - Adicionar item a uma doação
- `removerItem(int $itemId)` - Remover item de uma doação
- `atualizarItem(int $itemId, array $dados)` - Atualizar item de doação
- `excluir(int $id)` - Excluir doação

---

### **5.5 ItemDoacaoService**
**Responsabilidade:** Gerenciar itens individuais de doações

**Funcionalidades:**
- `listarPorDoacao(int $doacaoId)` - Listar itens de uma doação
- `buscarPorId(int $id)` - Buscar item por ID
- `criar(array $dados)` - Criar novo item
- `atualizar(int $id, array $dados)` - Atualizar item existente
- `excluir(int $id)` - Excluir item
- `calcularTotalDoacao(int $doacaoId)` - Calcular total de itens de uma doação

---

## 🔐 **6. Autenticação e Autorização Services**

### **6.1 AuthService**
**Responsabilidade:** Gerenciar autenticação de usuários

**Funcionalidades:**
- `login(string $email, string $senha)` - Autenticar usuário
- `logout()` - Encerrar sessão
- `registrar(array $dados)` - Registrar novo usuário
- `recuperarSenha(string $email)` - Solicitar recuperação de senha
- `redefinirSenha(string $token, string $novaSenha)` - Redefinir senha
- `verificarEmail(string $token)` - Verificar email do usuário
- `renovarToken()` - Renovar token de autenticação

---

### **6.2 PermissaoService**
**Responsabilidade:** Gerenciar permissões e autorizações

**Funcionalidades:**
- `verificarPermissao(int $usuarioId, string $permissao)` - Verificar se usuário tem permissão
- `verificarPerfil(int $usuarioId, string $perfil)` - Verificar se usuário tem perfil
- `listarPermissoes(int $usuarioId)` - Listar todas as permissões do usuário
- `atribuirPermissao(int $usuarioId, string $permissao)` - Atribuir permissão
- `removerPermissao(int $usuarioId, string $permissao)` - Remover permissão

---

## 📊 **7. Relatórios e Estatísticas Services**

### **7.1 DashboardService**
**Responsabilidade:** Gerar dados para dashboard administrativo

**Funcionalidades:**
- `obterEstatisticasGerais()` - Obter estatísticas gerais do sistema
- `obterEstatisticasMissoes()` - Estatísticas de missões
- `obterEstatisticasDoacoes()` - Estatísticas de doações
- `obterEstatisticasVoluntarios()` - Estatísticas de voluntários
- `obterEstatisticasNoticias()` - Estatísticas de notícias
- `obterAtividadesRecentes()` - Obter atividades recentes do sistema

---

### **7.2 RelatorioService**
**Responsabilidade:** Gerar relatórios diversos

**Funcionalidades:**
- `gerarRelatorioMissoes(array $filtros)` - Gerar relatório de missões
- `gerarRelatorioDoacoes(array $filtros)` - Gerar relatório de doações
- `gerarRelatorioVoluntarios(array $filtros)` - Gerar relatório de voluntários
- `gerarRelatorioPontosColeta(array $filtros)` - Gerar relatório de pontos de coleta
- `exportarRelatorio(string $tipo, array $filtros, string $formato)` - Exportar relatório (PDF, Excel, CSV)

---

## 🔔 **8. Notificações Services**

### **8.1 NotificacaoService**
**Responsabilidade:** Gerenciar notificações do sistema

**Funcionalidades:**
- `enviarNotificacao(int $usuarioId, string $tipo, array $dados)` - Enviar notificação
- `listarPorUsuario(int $usuarioId)` - Listar notificações de um usuário
- `marcarComoLida(int $id)` - Marcar notificação como lida
- `marcarTodasComoLidas(int $usuarioId)` - Marcar todas como lidas
- `excluir(int $id)` - Excluir notificação
- `enviarNotificacaoCandidaturaAprovada(int $candidaturaId)` - Notificar aprovação de candidatura
- `enviarNotificacaoMissaoCriada(int $missaoId)` - Notificar criação de missão
- `enviarNotificacaoDoacaoRecebida(int $doacaoId)` - Notificar recebimento de doação

---

## 📧 **9. Comunicação Services**

### **9.1 EmailService**
**Responsabilidade:** Gerenciar envio de emails

**Funcionalidades:**
- `enviarEmail(string $para, string $assunto, string $template, array $dados)` - Enviar email genérico
- `enviarEmailBoasVindas(int $pessoaId)` - Enviar email de boas-vindas
- `enviarEmailAprovacaoVoluntario(int $voluntarioId)` - Enviar email de aprovação
- `enviarEmailCandidaturaAprovada(int $candidaturaId)` - Enviar email de candidatura aprovada
- `enviarEmailDoacaoConfirmada(int $doacaoId)` - Enviar email de confirmação de doação

---

## 🗺️ **10. Geolocalização Services**

### **10.1 GeolocalizacaoService**
**Responsabilidade:** Gerenciar funcionalidades de geolocalização

**Funcionalidades:**
- `calcularDistancia(float $lat1, float $lon1, float $lat2, float $lon2)` - Calcular distância entre dois pontos
- `buscarPontosProximos(float $latitude, float $longitude, float $raioKm)` - Buscar pontos próximos
- `buscarMissoesProximas(float $latitude, float $longitude, float $raioKm)` - Buscar missões próximas
- `validarCoordenadas(float $latitude, float $longitude)` - Validar coordenadas geográficas
- `obterEnderecoPorCoordenadas(float $latitude, float $longitude)` - Obter endereço por coordenadas (geocoding reverso)
- `obterCoordenadasPorEndereco(string $endereco)` - Obter coordenadas por endereço (geocoding)

---

## 📁 **11. Upload e Arquivos Services**

### **11.1 UploadService**
**Responsabilidade:** Gerenciar uploads de arquivos

**Funcionalidades:**
- `uploadImagem($arquivo, string $pasta, ?array $opcoes = [])` - Upload de imagem com validação
- `uploadDocumento($arquivo, string $pasta)` - Upload de documento
- `excluirArquivo(string $caminho)` - Excluir arquivo do storage
- `redimensionarImagem(string $caminho, int $largura, int $altura)` - Redimensionar imagem
- `validarImagem($arquivo)` - Validar arquivo de imagem
- `obterUrlPublica(string $caminho)` - Obter URL pública do arquivo

---

## 🔍 **12. Busca Services**

### **12.1 BuscaService**
**Responsabilidade:** Gerenciar buscas no sistema

**Funcionalidades:**
- `buscarGlobal(string $termo, array $tipos = [])` - Busca global em todas as entidades
- `buscarMissoes(string $termo, array $filtros = [])` - Buscar missões
- `buscarNoticias(string $termo, array $filtros = [])` - Buscar notícias
- `buscarPessoas(string $termo, array $filtros = [])` - Buscar pessoas
- `buscarPontosColeta(string $termo, array $filtros = [])` - Buscar pontos de coleta
- `sugerirTermos(string $termo)` - Sugerir termos de busca

---

## 📝 **13. Validação Services**

### **13.1 ValidacaoService**
**Responsabilidade:** Centralizar validações comuns

**Funcionalidades:**
- `validarCpf(string $cpf)` - Validar formato de CPF
- `validarCnpj(string $cnpj)` - Validar formato de CNPJ
- `validarEmail(string $email)` - Validar formato de email
- `validarTelefone(string $telefone)` - Validar formato de telefone
- `validarCep(string $cep)` - Validar formato de CEP
- `formatarCpf(string $cpf)` - Formatar CPF
- `formatarTelefone(string $telefone)` - Formatar telefone
- `formatarCep(string $cep)` - Formatar CEP

---

## 📅 **14. Agendamento Services**

### **14.1 AgendamentoService**
**Responsabilidade:** Gerenciar agendamentos e tarefas agendadas

**Funcionalidades:**
- `agendarTarefa(string $tarefa, \DateTime $dataHora, array $dados = [])` - Agendar tarefa
- `listarTarefasAgendadas()` - Listar tarefas agendadas
- `cancelarTarefa(int $id)` - Cancelar tarefa agendada
- `executarTarefasPendentes()` - Executar tarefas pendentes
- `agendarNotificacaoMissao(int $missaoId)` - Agendar notificação de missão
- `agendarAtualizacaoStatusMissoes()` - Agendar atualização automática de status de missões

---

## 🔄 **15. Sincronização Services**

### **15.1 SincronizacaoService**
**Responsabilidade:** Gerenciar sincronização de dados externos

**Funcionalidades:**
- `sincronizarCidadesIBGE()` - Sincronizar cidades com dados do IBGE
- `sincronizarEstadosIBGE()` - Sincronizar estados com dados do IBGE
- `atualizarCoordenadasEnderecos()` - Atualizar coordenadas de endereços
- `validarDadosSincronizados()` - Validar dados sincronizados

---

## 📋 **Resumo por Prioridade**

### **Alta Prioridade (MVP)**
1. PessoaService
2. VoluntarioService
3. MissaoService
4. CandidaturaMissaoService
5. DoacaoService
6. PontoColetaService
7. NecessidadePontoService
8. NoticiaService
9. AuthService
10. UploadService

### **Média Prioridade**
1. CategoriaMissaoService
2. CategoriaNoticiaService
3. TipoItemService
4. EnderecoService
5. CidadeService / EstadoService / PaisService
6. PerfilService
7. ImagemNoticiaService
8. ItemDoacaoService
9. NotificacaoService
10. EmailService

### **Baixa Prioridade (Melhorias)**
1. DashboardService
2. RelatorioService
3. GeolocalizacaoService
4. BuscaService
5. ValidacaoService
6. AgendamentoService
7. SincronizacaoService

---

## 📌 **Observações Importantes**

1. **Transações:** Services que manipulam múltiplas entidades devem usar transações de banco de dados
2. **Validações:** Todas as services devem validar dados antes de persistir
3. **Tratamento de Erros:** Implementar tratamento adequado de exceções
4. **Logs:** Registrar operações importantes para auditoria
5. **Cache:** Considerar cache para consultas frequentes
6. **Paginação:** Implementar paginação em listagens
7. **Filtros:** Suportar filtros avançados nas listagens
8. **Soft Delete:** Considerar soft delete para entidades importantes
9. **Eventos:** Disparar eventos Laravel para ações importantes
10. **Testes:** Criar testes unitários e de integração para cada service

