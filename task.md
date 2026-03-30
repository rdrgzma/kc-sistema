# Backlog de Desenvolvimento – Sistema Jurídico K&C 2.0

**Stack:** Laravel 13, PHP 8.4, Livewire v4, Filament v5 (Headless), TailwindCSS v4, MySQL, Reverb, sqlite.

---

## ✅ ÉPICOS CONCLUÍDOS (Fundação, Core e UX)

<details>
<summary>Clique para expandir as tarefas já finalizadas</summary>

### ÉPICO 1: Fundação e Infraestrutura (Setup)
- [x] Inicializar projeto e configurar banco local (sqlite).
- [x] Configurar layout base, TailwindCSS v4 e Filament Headless.
- [x] Implementar ACL (spatie/laravel-permission) com Roles definidas (Admin, Sócio, Advogado, Operacional).
- [x] Configurar rastreabilidade de auditoria (spatie/laravel-activitylog).
- [x] Criar a Trait `HasLegacyData` para futura migração.

### ÉPICO 2: Módulo Base (Pessoas e Lookups)
- [x] Criar estrutura de Lookups (Áreas, Fases, Procedimentos, Sentenças).
- [x] Desenvolver CRUD unificado de Pessoas (PF/PJ) com Filament.
- [x] Criar estrutura para Seguradoras e Profissionais (Peritos/Assistentes).

### ÉPICO 3: Módulo Processual e Mérito
- [x] Criar estrutura de Processos (incluindo `economia_gerada` e `perda_estimada`).
- [x] Construir interface "Case View" centralizada.
- [x] Configurar logs de auditoria automáticos para Processos.

### ÉPICO 4: Módulo Gestão de Rotina (GR) Avançado
- [x] Implementar Onboarding de Fluxo (Wizard transacional).
- [x] Criar estrutura polimórfica para Documentos.
- [x] Desenvolver estrutura e registro de Interações (WhatsApp, Ligações, Reuniões).

### ÉPICO EXTRA: UI/UX Global
- [x] Dark Mode dinâmico e Sidebars reativas (Alpine + LocalStorage).
- [x] Componentização de tabelas em Modais e Tooltips nativos.
</details>

---

## 🚀 ÉPICOS EM ANDAMENTO E PENDENTES

### ÉPICO 5: Módulo Financeiro Avançado (Rateio e Automação)
*Foco: Finalizar a inteligência financeira e cobrir o requisito de automação de custas.*
- [x] Criar a Migration e o Model de Lançamentos Financeiros e Categorias.
- [x] Implementar o CRUD básico de receitas e despesas vinculadas a processos.
- [x ] **[NOVO]** Implementar Automação de Custas: Criar Observers ou Actions para gerar lançamentos automáticos de despesas processuais baseados em gatilhos de andamento.
- [x ] Desenvolver a estrutura de Rateio de Honorários (Migration Pivot entre Lançamento e User).
- [x ] Criar Service de Cálculo: Lógica para divisão automática de honorários baseada em percentual de êxito ou horas trabalhadas.
- [x ] Implementar Policies de Visibilidade Financeira: Bloquear rigorosamente o acesso do nível "Operacional/Secretaria" a qualquer dado de rendimento financeiro.

### ÉPICO 6: Planner, Colaboração e Timeline
*Foco: Concluir as ferramentas visuais de organização da equipe.*
- [x] Desenvolver a base do Planner (Migrations e Models).
- [x] Criar o componente Kanban interativo em Livewire (drag-and-drop).
- [x] Desenvolver a estrutura polimórfica de Comentários Inline Threads.
- [x] Integrar Comentários nas Tarefas, Documentos e Processos.
- [x] Criar a fundação da Timeline (Eventos gerados no Onboarding).
- [x] Finalizar a injeção automática de eventos na Timeline a partir de mudanças de fase e registros financeiros.

### ÉPICO 7: Relatórios, Dashboards e Notificações (Real-time)
*Foco: Extração de valor dos dados e tempo real (Infra já instalada no composer).*
- [ ] Configurar os Eventos de Broadcast utilizando o Laravel Reverb (já instalado) (ex: `NovoAndamentoRegistrado`, `HonorarioLiberado`, `AlertaPrazo`).
- [ ] Integrar Laravel Echo no frontend (Livewire `#[On]` ou script JS) para exibir notificações instantâneas ("Toast") sem reload da página.
- [ ] Desenvolver os widgets do Dashboard (Chart.js / ApexCharts): Gráficos de rendimento por profissional (diário, semanal, mensal e anual).
- [ ] Desenvolver a Exportação de Dados (Painel de Produtividade e Auditoria Externa) utilizando o `maatwebsite/excel` (já instalado).

### ÉPICO 8: Migração de Dados (Legado K&C)
*Foco: Resgatar histórico do sistema antigo de forma íntegra.*
- [ ] Configurar conexão secundária de banco de dados (`database.php`) para acessar o MySQL legado.
- [ ] Desenvolver Console Commands (`php artisan migrate:legacy-pessoas`, `migrate:legacy-processos`).
- [ ] Escrever a lógica de normalização de dados e inserção utilizando a Trait `HasLegacyData`.
- [ ] Executar testes de integridade dos dados migrados em ambiente de homologação.

### ÉPICO 9: Deploy e CI/CD (Produção)
*Foco: Empacotamento, entrega contínua e infraestrutura cloud.*
- [ ] Criar `Dockerfile` otimizado para produção (PHP 8.4 + Extensões).
- [ ] Criar `docker-compose.yml` para orquestração dos serviços (App, Nginx, Redis, Reverb).
- [ ] Desenvolver o workflow do GitHub Actions (Testes automatizados + Build de imagem para o Docker Hub).
- [ ] Configurar o deploy automatizado no servidor de produção via Portainer.
- [ ] Configurar o roteamento e emissão de certificados SSL automáticos através do Traefik.