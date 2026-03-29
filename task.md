# Backlog de Desenvolvimento – Sistema Jurídico K&C 2.0

**Stack:** Laravel 13, PHP 8.4, Livewire v4, Filament v5  .(Headless), TailwindCSS v4, MySQL, Reverb, sqlite.

## ÉPICO 1: Fundação e Infraestrutura (Setup)
- [x] Inicializar o projeto Laravel 13.
- [x] Configurar o banco de dados sqlite para o ambiente de desenvolvimento local.
- [x] Criar o repositório no GitHub sob o namespace `rdrgzma/kc-sistema` e realizar o primeiro commit.
- [x] Instalar o Livewire v4 e configurar o layout base com TailwindCSS v4.
- [x] Instalar os pacotes `filament/forms`, `filament/tables` e `filament/actions` (Headless).
- [x] Instalar e configurar o `spatie/laravel-permission` (Migrations e Seeders das Roles: Admin, Sócio, Advogado, Operacional).
- [x] Instalar e publicar as configurações do `spatie/laravel-activitylog`.
- [x] Criar a Trait `HasLegacyData` para o mapeamento futuro do banco antigo.

## ÉPICO 2: Módulo Base (Pessoas e Lookups)
- [x] Criar Migrations, Models e Factories para as tabelas de apoio (Lookups: Áreas, Fases, Procedimentos, Sentenças).
- [x] Desenvolver a estrutura unificada de Pessoas (PF e PJ) (Migration, Model, Controller/Livewire).
- [x] Construir o formulário de criação/edição de Pessoas usando Filament Forms no frontend.
- [x] Construir a tabela de listagem de Pessoas com Filament Tables (filtros por tipo, CPF/CNPJ).
- [x] Criar Migrations e Models para Seguradoras e Profissionais (Peritos, Assistentes).

## ÉPICO 3: Módulo Processual e Mérito (Core)
- [x] Criar a Migration e o Model principal de `Processos`, incluindo os campos de `economia_gerada` e `perda_estimada`.
- [x] Desenvolver o formulário complexo de Processos (vinculando Pessoa, Seguradora, Lookups de Fases).
- [x] Implementar a tabela de listagem de Processos com busca avançada e paginação otimizada.
- [x] Criar a interface "Case View" (página de detalhes do processo centralizando todas as informações).
- [x] Configurar o registro automático de logs (Activitylog) no Model de Processos.

## ÉPICO 4: Módulo Gestão de Rotina (GR) Avançado
- [x] Desenvolver a estrutura polimórfica de Documentos (Migration, Model).
- [x] Criar o componente Livewire de upload múltiplo de Documentos (anexáveis em Pessoas ou Processos).
- [x] Desenvolver a estrutura de Interações (Migration, Model para WhatsApp, Ligações, Reuniões).
- [x] Criar o formulário de registro de Interações dentro do "Case View" e do perfil do cliente.
- [x] Implementar o Onboarding de Fluxo (wizard simplificado com transações atômicas para cadastro automatizado).

## ÉPICO 5: Módulo Financeiro e Rendimento Profissional
- [x] Criar a Migration e o Model de Lançamentos Financeiros e Categorias.
- [x] Implementar o CRUD de receitas e despesas vinculadas a processos.
- [ ] Desenvolver a estrutura de Rateio de Honorários (Migration Pivot entre Lançamento e User).
- [ ] Criar a lógica de cálculo (Service) para divisão automática de honorários baseada em percentual de êxito.
- [ ] Construir a interface de visualização financeira restrita (Policies para bloquear acesso do nível "Operacional").

## ÉPICO 6: Planner, Colaboração e Timeline
- [x ] Desenvolver a base do Planner (Migrations de Planners, Buckets, Tasks e Progressos).
- [x ] Criar o componente Kanban interativo em Livewire com drag-and-drop.
- [x ] Desenvolver a estrutura polimórfica de Comentários (Inline Threads).
- [x ] Integrar os Comentários nas Tarefas, Documentos e Processos.
- [x] Criar a fundação da Timeline (Migration/Model TimelineEvent gerado automaticamente no Onboarding).

## ÉPICO 7: Relatórios, Dashboards e Notificações (Real-time)
- [ ] Instalar o Laravel Reverb e configurar o Laravel Echo no frontend.
- [ ] Criar os eventos de broadcast (ex: `NovoAndamentoRegistrado`, `HonorarioLiberado`).
- [ ] Desenvolver os widgets do Dashboard (Chart.js) para rendimento diário/semanal/mensal por advogado.
- [ ] Instalar o `maatwebsite/excel` e criar Actions de exportação na tabela de Produtividade e Processos.

## ÉPICO 8: Migração de Dados (Legado K&C)
- [ ] Configurar conexão secundária de banco de dados (`database.php`) para acessar o MySQL legado.
- [ ] Desenvolver Console Commands (`php artisan migrate:legacy-pessoas`, `migrate:legacy-processos`).
- [ ] Escrever a lógica de normalização de dados e inserção utilizando a Trait `HasLegacyData`.
- [ ] Executar testes de integridade dos dados migrados.

## ÉPICO 9: Deploy e CI/CD (Produção)
- [ ] Configurar os arquivos `Dockerfile` e `docker-compose.yml` para os serviços da aplicação (PHP, Nginx, Redis, Reverb).
- [ ] Desenvolver o workflow do GitHub Actions para testes automatizados e build da imagem para o Docker Hub.
- [ ] Configurar o deploy automatizado no servidor de produção gerenciado pelo Portainer.
- [ ] Configurar o roteamento e certificados SSL através do Traefik.

## ÉPICO EXTRA: UI/UX Global e Otimizações (Concluídos)
- [x] Implementar Dark Mode dinâmico no App-Layout salvo via `localStorage`.
- [x] Configurar sistema de Sidebars recolhível (w-64 para w-20) reativo via Alpine.js e LocalStorage.
- [x] Introduzir Heroicons discretos e Native CSS Tooltips suspensos (Visíveis apenas com Sidebar fechada).
- [x] Transformar tabelas isoladas do Filament (Pessoas/Processos) em componentes renderizáveis via Modais.
- [x] Correção do ecossistema front-end do Laravel Echo mitigando crash de dependências (Pusher) no build.