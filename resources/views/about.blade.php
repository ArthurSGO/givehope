@extends('app')
@section('title', 'Sobre')

@section('content')
    <div class="about-page pb-5">
        <section class="about-hero text-center text-white position-relative overflow-hidden rounded-4 shadow-sm">
            <div class="hero-overlay"></div>
            <div class="hero-content position-relative">
                <span class="badge rounded-pill text-bg-light text-uppercase fw-semibold px-3 py-2 mb-3">Nossa
                    História</span>
                <h1 class="display-5 fw-bold mb-3">Transparência e solidariedade que transformam realidades</h1>
                <p class="lead mb-4">O GiveHope nasceu para aproximar quem deseja ajudar de quem mais precisa, oferecendo
                    uma experiência
                    clara, acolhedora e acessível para todos os envolvidos.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <div class="stat-card">
                        <span class="stat-number">+2k</span>
                        <span class="stat-label">Doadores ativos</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">89%</span>
                        <span class="stat-label">Campanhas concluídas</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">24h</span>
                        <span class="stat-label">Atualizações em tempo real</span>
                    </div>
                </div>
            </div>
            <div class="floating-shape"></div>
            <div class="floating-shape floating-shape-2"></div>
        </section>

        <section class="mt-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="p-4 rounded-4 shadow-sm bg-body">
                        <h2 class="fw-semibold mb-3">Nossa missão</h2>
                        <p class="text-muted mb-4">Acreditamos que cada gesto de solidariedade deve ser celebrado e
                            acompanhado de perto. Por isso,
                            desenvolvemos uma plataforma que reúne campanhas, doadores e comunidades em um mesmo ambiente,
                            mostrando com clareza o impacto de cada contribuição.</p>
                        <ul class="list-unstyled d-grid gap-3 mb-0">
                            <li class="d-flex gap-3 align-items-start">
                                <div class="icon-circle text-primary"><i class="fa-solid fa-hand-holding-heart"></i></div>
                                <div>
                                    <h5 class="mb-1">Conexões de confiança</h5>
                                    <p class="text-muted mb-0">Informações detalhadas de campanhas, com metas, responsáveis
                                        e prestações de contas.</p>
                                </div>
                            </li>
                            <li class="d-flex gap-3 align-items-start">
                                <div class="icon-circle text-success"><i class="fa-solid fa-seedling"></i></div>
                                <div>
                                    <h5 class="mb-1">Impacto contínuo</h5>
                                    <p class="text-muted mb-0">Acompanhamento em tempo real para incentivar novas doações e
                                        fortalecer a rede de apoio.</p>
                                </div>
                            </li>
                            <li class="d-flex gap-3 align-items-start">
                                <div class="icon-circle text-warning"><i class="fa-solid fa-shield-heart"></i></div>
                                <div>
                                    <h5 class="mb-1">Segurança em primeiro lugar</h5>
                                    <p class="text-muted mb-0">Processos verificados e monitoramento ativo para garantir a
                                        integridade das ações.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-card shadow-sm bg-body rounded-4 p-4 h-100">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="icon-circle-lg text-info"><i class="fa-solid fa-chart-line"></i></div>
                                    <h5 class="mb-0">Relatórios claros e acessíveis</h5>
                                </div>
                                <p class="text-muted mb-0">Gráficos e indicadores simplificados ajudam gestores e doadores a
                                    acompanharem metas e resultados.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card shadow-sm bg-body rounded-4 p-4 h-100">
                                <div class="icon-circle-sm bg-soft-primary text-primary mb-3"><i
                                        class="fa-solid fa-laptop"></i></div>
                                <h6 class="fw-semibold mb-2">Plataforma intuitiva</h6>
                                <p class="text-muted mb-0">Interface responsiva e organizada, pensada para ser leve e
                                    eficiente em qualquer dispositivo.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card shadow-sm bg-body rounded-4 p-4 h-100">
                                <div class="icon-circle-sm bg-soft-success text-success mb-3"><i
                                        class="fa-solid fa-users"></i></div>
                                <h6 class="fw-semibold mb-2">Comunidade envolvida</h6>
                                <p class="text-muted mb-0">Comunicação direta entre paróquias, voluntários e doadores para
                                    unir esforços em um mesmo propósito.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 pt-4">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4">
                    <div class="value-card h-100 rounded-4 shadow-sm p-4">
                        <div class="value-badge mb-3"><i class="fa-solid fa-compass"></i></div>
                        <h4 class="fw-semibold mb-2">Nossos valores</h4>
                        <p class="text-muted mb-0">Somos guiados pela confiança, empatia e responsabilidade, fortalecendo
                            redes solidárias que geram esperança.</p>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="timeline rounded-4 shadow-sm bg-body p-4 p-lg-5 h-100">
                        <h4 class="fw-semibold mb-4">Como trabalhamos</h4>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary-subtle text-primary"><i
                                    class="fa-solid fa-lightbulb"></i></div>
                            <div>
                                <h6 class="fw-semibold mb-1">1. Identificamos as necessidades</h6>
                                <p class="text-muted mb-0">Paróquias e comunidades apresentam seus projetos com metas claras
                                    e responsáveis definidos.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success-subtle text-success"><i
                                    class="fa-solid fa-handshake-angle"></i></div>
                            <div>
                                <h6 class="fw-semibold mb-1">2. Conectamos com doadores</h6>
                                <p class="text-muted mb-0">Campanhas ficam visíveis para toda a comunidade, com relatos,
                                    fotos e necessidades detalhadas.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning-subtle text-warning"><i
                                    class="fa-solid fa-circle-nodes"></i></div>
                            <div>
                                <h6 class="fw-semibold mb-1">3. Acompanhamos cada etapa</h6>
                                <p class="text-muted mb-0">Atualizações constantes mostram o progresso das arrecadações e
                                    orientam próximos passos.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info-subtle text-info"><i class="fa-solid fa-gift"></i></div>
                            <div>
                                <h6 class="fw-semibold mb-1">4. Celebramos os resultados</h6>
                                <p class="text-muted mb-0">Relatórios gerais e histórias inspiradoras evidenciam o impacto
                                    coletivo de cada campanha.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5 pt-4">
            <div class="cta-card rounded-4 shadow-sm p-5 text-center">
                <h2 class="fw-semibold mb-3">Junte-se a nós nessa corrente do bem</h2>
                <p class="text-muted mb-4">Faça parte de uma plataforma feita para quem deseja transformar realidades com
                    responsabilidade e carinho.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('seek') }}" class="btn btn-primary px-4 py-2">Acompanhe uma doação</a>
                    <a href="{{ route('soon') }}" class="btn btn-outline-primary px-4 py-2">Descubra campanhas</a>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .about-hero {
            background: linear-gradient(135deg, #5a6ff0, #7fd7e0);
            padding: clamp(3rem, 5vw, 5rem);
        }

        .about-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.25) 0, transparent 55%),
                radial-gradient(circle at 80% 0%, rgba(255, 255, 255, 0.15) 0, transparent 50%);
            opacity: .7;
        }

        .about-hero .hero-content {
            position: relative;
            z-index: 1;
        }

        .about-hero .stat-card {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.15);
            border-radius: 1rem;
            padding: 1.25rem 1.75rem;
            min-width: 160px;
        }

        .about-hero .stat-number {
            display: block;
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .about-hero .stat-label {
            font-size: .875rem;
            opacity: .85;
        }

        .floating-shape {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            bottom: -80px;
            right: -60px;
            filter: blur(0px);
        }

        .floating-shape-2 {
            width: 200px;
            height: 200px;
            top: -60px;
            left: -40px;
        }

        .icon-circle,
        .icon-circle-sm,
        .icon-circle-lg {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-circle {
            width: 3rem;
            height: 3rem;
            background: rgba(82, 120, 246, .12);
            font-size: 1.25rem;
        }

        .icon-circle-lg {
            width: 3.5rem;
            height: 3.5rem;
            background: rgba(79, 209, 197, .12);
            font-size: 1.5rem;
        }

        .icon-circle-sm {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1rem;
        }

        .bg-soft-primary {
            background: rgba(82, 120, 246, .12);
        }

        .bg-soft-success {
            background: rgba(0, 200, 83, .15);
        }

        .value-card {
            background: linear-gradient(160deg, rgba(90, 111, 240, 0.12), rgba(127, 215, 224, 0.12));
        }

        .value-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: .75rem;
            background: rgba(255, 255, 255, .6);
            color: #4b5be0;
            font-size: 1.25rem;
        }

        .timeline {
            position: relative;
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 1.5rem;
            bottom: 1.5rem;
            left: 1.25rem;
            width: 2px;
            background: rgba(82, 120, 246, .2);
        }

        .timeline-item {
            position: relative;
            padding-left: 3.75rem;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
        }

        .timeline-item+.timeline-item {
            margin-top: 2rem;
        }

        .timeline-marker {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 0.25rem 0.75rem rgba(82, 120, 246, .15);
            position: absolute;
            left: 0;
            top: 0;
            transform: translate(-50%, 0);
        }

        .cta-card {
            background: linear-gradient(135deg, rgba(82, 120, 246, 0.12), rgba(127, 215, 224, 0.12));
        }

        @media (max-width: 991.98px) {
            .timeline::before {
                left: 0.75rem;
            }

            .timeline-item {
                padding-left: 3.25rem;
            }
        }

        @media (max-width: 575.98px) {
            .about-hero {
                padding: 2.5rem 1.75rem;
            }

            .about-hero .stat-card {
                min-width: 120px;
                padding: 1rem 1.25rem;
            }

            .timeline::before {
                left: .5rem;
            }

            .timeline-item {
                padding-left: 2.75rem;
            }
        }
    </style>
@endpush