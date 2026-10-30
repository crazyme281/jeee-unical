@extends('layouts.jetsi')

@section('title', 'About us')
@section('nav', 'about')

@section('content')

@php
// Journal scope. 'numbered' => true shows a numbered list, false shows a bulleted list.
$scope = [
    ['title' => 'Mechanical Engineering', 'numbered' => true, 'items' => [
        'Sustainable energy systems (solar-thermal, wind, biomass, waste-to-energy)',
        'Thermo-fluids, heat transfer, and HVAC systems',
        'Advanced materials, composites, and manufacturing technologies',
        'Sustainable machine design and automation',
        'Robotics, mechatronics, and control engineering',
        'Renewable-energy storage systems (thermal, mechanical, hybrid)',
        'Energy efficiency, exergy analysis, and system optimization',
        'Automotive engineering, propulsion, and emissions reduction',
        'Computational fluid dynamics (CFD) and finite-element analysis (FEA)',
        'Refrigeration, cryogenics, and low-carbon cooling technologies',
    ]],
    ['title' => 'Civil Engineering', 'numbered' => true, 'items' => [
        'Sustainable construction materials and green building technologies',
        'Structural engineering and infrastructure resilience',
        'Water resources engineering, hydrology, and climate-adaptive design',
        'Transportation engineering and smart mobility systems',
        'Environmental engineering, pollution control, and waste management',
        'Geotechnical engineering and soil improvement for sustainability',
        'Urban planning, smart cities, and sustainable development frameworks',
        'Coastal and hydraulic engineering',
        'Disaster risk reduction and climate-resilient infrastructure',
    ]],
    ['title' => 'Electrical & Electronics Engineering', 'numbered' => true, 'items' => [
        'Renewable power systems: solar PV, wind, hydro, hybrid microgrids',
        'Power system planning, protection, and smart grid technologies',
        'Energy management systems and demand-side optimization',
        'Control systems, automation, and instrumentation',
        'Power electronics for sustainable energy conversion',
        'Embedded systems, IoT, and smart energy devices',
        'Sustainable telecommunications and ICT infrastructure',
        'High-voltage engineering and power quality improvement',
    ]],
    ['title' => 'Chemical Engineering', 'numbered' => true, 'items' => [
        'Sustainable chemical process design and optimization',
        'Green chemistry and environmentally benign processes',
        'Biofuels, biogas, and biomass conversion technologies',
        'Nanomaterials, catalysis, and advanced material synthesis',
        'Process safety, risk analysis, and hazard mitigation',
        'Water treatment, desalination, and environmental remediation',
        'Polymer engineering and composite materials',
        'Life cycle assessment (LCA) of chemical processes',
    ]],
    ['title' => 'Petroleum & Process Engineering', 'numbered' => true, 'items' => [
        'Sustainable oil & gas production technologies',
        'Enhanced oil recovery (EOR) and carbon-capture-assisted recovery',
        'Drilling engineering, wellbore stability, and geothermal drilling',
        'Reservoir engineering and simulation',
        'Pipeline engineering, flow assurance, and corrosion control',
        'Refining, petrochemical processing, and cleaner production methods',
        'Gas processing, LNG, LPG, and flare-gas utilization',
        'Transition pathways from fossil fuels to low-carbon energy systems',
        'Health, safety, and environment (HSE) in petroleum operations',
    ]],
    ['title' => 'Sustainable & Renewable Energy Technologies', 'numbered' => false, 'items' => [
        'Solar, wind, biomass, geothermal, hydrogen, nuclear, hybrid systems',
        'Energy efficiency and management',
        'Smart grids, microgrids, energy storage systems',
    ]],
    ['title' => 'Climate Change, Environment & Green Technologies', 'numbered' => false, 'items' => [
        'Climate modelling and mitigation',
        'Carbon capture, utilisation and storage (CCUS)',
        'Waste management',
        'Environmental impact assessment',
        'Circular economy and industrial ecology',
    ]],
    ['title' => 'Emerging Engineering Technologies', 'numbered' => false, 'items' => [
        'Artificial intelligence & machine learning applications in engineering',
        'IoT, robotics, automation, Industry 4.0',
        'Nanotechnology, smart materials, additive manufacturing',
        'Engineering modelling, simulation (CFD, FEM, FEA)',
    ]],
    ['title' => 'Engineering Management, Policy & Economics', 'numbered' => false, 'items' => [
        'Engineering economics and cost analysis',
        'Infrastructure management',
        'Energy policy and planning',
        'Sustainable industrial development',
    ]],
];
@endphp

  <div class="page-head">
    <div class="container"><h1>About Us</h1></div>
  </div>

  <main id="main" class="container page-layout">

    <aside class="toc" aria-label="On this page">
      <h2>On this page</h2>
      <nav>
        <a href="#about">About</a>
        <a href="#scope">Scope</a>
        <a href="#editorial">Editorial board</a>
        <a href="#guidelines">Author guidelines</a>
      </nav>
    </aside>

    <div class="doc">

      <!-- 1. ABOUT -->
      <section id="about">
        <h2>Journal of Engineering Technology and Sustainable Innovation (JETSI)</h2>
        <p class="lead">p-ISSN 3141-2947</p>
        <h3 class="sub">Focus and scope</h3>
        <p>The Journal of Engineering Technology and Sustainable Innovation (JETSI) is a multidisciplinary peer-reviewed journal dedicated to advancing innovative engineering solutions that promote sustainable development, environmental stewardship, industrial growth, and technological transformation. The journal provides a platform for high-quality research, reviews, case studies, and technical reports that address contemporary engineering challenges through sustainable and interdisciplinary approaches. JETSI encourages contributions that integrate scientific principles, engineering applications, policy frameworks, and emerging technologies aimed at improving energy systems, infrastructure, manufacturing, industrial processes, environmental management, and socio-economic development.</p>
        <p>The journal covers a broad spectrum of engineering disciplines including Mechanical, Civil, Electrical and Electronics, Chemical, Petroleum and Process, Agricultural and Bioresources, Computer, Environmental, Materials, and Industrial Engineering. Areas of interest include renewable and sustainable energy technologies such as solar, wind, hydro, biomass, geothermal, hydrogen, hybrid systems, smart grids, energy storage, energy efficiency, and low-carbon technologies. The journal also welcomes studies in sustainable construction materials, resilient infrastructure, transportation systems, water resources, waste management, pollution control, climate adaptation, smart cities, and green building technologies.</p>
        <p>JETSI further embraces research in advanced manufacturing, robotics, automation, mechatronics, artificial intelligence, machine learning, Internet of Things (IoT), nanotechnology, computational modelling, simulation, additive manufacturing, and Industry 4.0 applications in engineering systems. Contributions in process optimization, green chemistry, biofuels, carbon capture, cleaner production technologies, environmental remediation, petroleum transition technologies, safety engineering, engineering economics, innovation management, policy, and sustainable industrial development are also encouraged.</p>
        <p>The journal particularly promotes interdisciplinary studies that combine multiple engineering fields to develop practical, scalable, and economically viable solutions for global sustainability challenges. Emphasis is placed on research with measurable technological, environmental, and societal impact, including studies involving system integration, performance assessment, lifecycle analysis, climate mitigation, circular economy, and sustainable resource utilization. Through this broad and inclusive scope, JETSI aims to serve as a leading outlet for cutting-edge engineering research that supports sustainable innovation and national and global technological advancement.</p>
      </section>

      <!-- 2. SCOPE -->
      <section id="scope">
        <h2>Scope includes, but is not limited to:</h2>
        <div class="panel-grid">
          @foreach ($scope as $panel)
          @php $tag = $panel['numbered'] ? 'ol' : 'ul'; @endphp
          <article class="card panel">
            <h3>{{ $panel['title'] }}</h3>
            <{{ $tag }} class="{{ $panel['numbered'] ? 'list-num' : 'list-dot' }}">
              @foreach ($panel['items'] as $item)
              <li>{{ $item }}</li>
              @endforeach
            </{{ $tag }}>
          </article>
          @endforeach
        </div>
      </section>

      <!-- 3. EDITORIAL BOARD -->
      <section id="editorial">
        <h2>EDITORIAL BOARD</h2>
        <div class="panel-grid">
          <article class="card panel">
            <h3>Editorial Leadership</h3>
            <p class="label">Founding Editor</p>
            <p>Barth N. Ekwueme (Calabar – Nigeria)</p>
            <p class="label">Editor-in-Chief</p>
            <p>Professor Fidelis Abam</p>
            <p>Department of Mechanical Engineering, University of Calabar, Calabar, Nigeria</p>
            <p class="label">Deputy Editor-in-Chief</p>
            <p>Professor Ogri James Ushie</p>
          </article>
          <article class="card panel">
            <h3>Managing/Technical Editors</h3>
            <ul class="list-dot">
              <li>Dr. Edward Adah</li>
              <li>Dr. Cordelia Omoyi</li>
              <li>Dr. Ojong Elias Ojong</li>
              <li>Dr. Paschal Ubi</li>
              <li>Dr. Okon Edet Ita</li>
            </ul>
          </article>
          <article class="card panel">
            <h3>Journal Secretary and Production</h3>
            <p class="label">Journal Secretary</p>
            <p>Dr. Chememe Ekwueme</p>
            <p class="label">Production Editors/Web Administrator</p>
            <ul class="list-dot">
              <li>Dr. Patrick Adah</li>
              <li>Engr. Victor Ekpang</li>
              <li>Engr. Eludire Oluwadamilare</li>
            </ul>
          </article>
          <article class="card panel">
            <h3>Editorial Office</h3>
            <p>Faculty of Engineering and Technology</p>
            <p>University of Calabar</p>
            <p>Calabar, Nigeria</p>
            <p><a href="{{ route('contact') }}">Full editorial board and contacts</a></p>
          </article>
        </div>
      </section>

      <!-- 4. AUTHOR GUIDELINES -->
      <section id="guidelines">
        <h2>AUTHOR GUIDELINES</h2>
        <p>Manuscripts are considered as research papers, review articles, technical notes, or case studies. Submission implies that the work is original and is not under review elsewhere.</p>
        <div class="panel-grid">
          <article class="card panel">
            <h3>Manuscript Structure</h3>
            <ol class="list-num">
              <li>Title</li>
              <li>Abstract</li>
              <li>Keywords</li>
              <li>Introduction</li>
              <li>Materials and Method</li>
              <li>Results and Discussion</li>
              <li>Conclusion</li>
              <li>Acknowledgements</li>
              <li>Conflict of Interest</li>
              <li>Author Contributions</li>
              <li>Data Availability Statement</li>
              <li>AI Declaration</li>
              <li>References</li>
            </ol>
          </article>
          <article class="card panel">
            <h3>Formatting Requirements</h3>
            <ul class="list-dot">
              <li>Paper size: A4, 25 mm margins</li>
              <li>Font: 12-point throughout</li>
              <li>Maximum length: 12 pages including tables, figures, and references</li>
              <li>Title: not more than 20 words, upper case</li>
              <li>Abstract: not more than 200 words</li>
              <li>Keywords: 4-6 after the abstract</li>
              <li>SI units; define symbols and abbreviations at first use</li>
            </ul>
            <p><a href="{{ route('guideline') }}">Read the full author guidelines</a></p>
          </article>
        </div>
      </section>

    </div>
  </main>

@endsection
