@extends('layouts.jetsi')

@section('title', 'Author Guidelines')

@section('content')
@php
// Guideline sections. Edit text here.
$sections = [
    ['Submission of Manuscript', ['Submission of a manuscript implies that the work is original, has not been previously published, and is not currently under review or consideration by any other journal or publication outlet. Authors are required to submit their manuscripts electronically through the journal\'s online submission system, where they can monitor the review process and track the status of their submissions from initial receipt to final decision.']],
    ['Preparation of the Manuscript', ['Authors should prepare manuscripts in clear English using the official JETSI template. Papers must be formatted on A4 paper with 25 mm margins and 12-point font throughout. The manuscript, including tables, figures, and references, should not exceed 12 pages. Submissions must follow the prescribed JETSI structure, including the Title, Abstract, Keywords, Introduction, Materials and Method, Results and Discussion, Conclusion, Acknowledgements, Conflict of Interest, Author Contributions, Data Availability Statement, AI Declaration, and References.']],
    ['Title/Abstract', ['The title page should contain the title of the paper, name(s) and affiliation(s) of the author(s) plus e-mail address and phone number of the corresponding author, and the abstract. The corresponding author should be indicated. The title of the paper should not exceed 20 words, in 12 font size, upper case, while abstract should not exceed 200 words. Four to six keywords should be provided after the abstract.']],
    ['Symbols, Abbreviations and Units', ['Symbols and abbreviations should be defined where first mentioned. SI units should be used.']],
    ['Introduction', ['Introduction: In accordance with the JETSI template, the Introduction should provide a clear context for the study by presenting the background, problem statement, and significance of the research. It should include a concise review of relevant and recent literature, identify existing knowledge gaps or unresolved challenges, and justify the need for the study. The section should conclude with a clearly defined research aim, specific objectives, and the expected contribution of the work to engineering, technology, and sustainable innovation.']],
    ['Theoretical Analysis', ['Mathematical Modelling and Equations: Where applicable, authors should clearly present the mathematical principles, theoretical frameworks, models, and governing equations underpinning the study. All equations must be prepared using an appropriate equation editor, numbered consecutively, and referenced within the text. Variables, parameters, symbols, and units should be defined at first use to ensure clarity, reproducibility, and consistency with the JETSI template.']],
    ['Materials and Method', ['In line with the JETSI template, this section should provide sufficient detail to enable the study to be understood and, where applicable, reproduced. It should describe the research design, study area, materials, apparatus and equipment used, experimental or simulation setup, data sources, procedures, and analytical techniques. Design methodologies, modelling approaches, validation procedures, software tools, and statistical methods should be clearly presented and justified. Relevant equations, assumptions, and operating conditions should also be included where necessary.']],
    ['Results and Discussion', ['In accordance with the JETSI template, this section should present the study findings in a clear and logical manner using text, tables, figures, charts, or other appropriate formats. Data presented in tables should not be duplicated in graphical form unless necessary for clarity. The discussion should critically interpret the results, compare them with relevant literature, highlight their scientific and practical significance, explain observed trends or deviations, and demonstrate how the findings address the study objectives and contribute to engineering, technology, and sustainable innovation.']],
    ['Conclusion', ['In line with the JETSI template, the conclusion should concisely summarize the principal findings of the study without introducing new information. It should clearly highlight the study\'s contributions to engineering, technology, and sustainable innovation, emphasize the practical, scientific, or policy relevance of the results, and provide recommendations, implications, or directions for future research where appropriate.']],
];
@endphp
  <section class="page-head">
    <div class="container">
      <h1>AUTHOR GUIDELINES</h1>
    </div>
  </section>

  <main id="main" class="guideline-page">
    <section class="section guideline-section">
      <div class="container">
        <div class="col-md-8">
          <p>The Journal of Engineering Technology and Sustainable Innovation (JETSI) publishes high-quality peer-reviewed articles spanning engineering, technology, and sustainability sciences. Manuscripts are considered as research papers, review articles, technical notes, or case studies that advance innovation, sustainable development, and technological progress.</p>

          <h4>Downloads</h4>
          <ul class="guideline-downloads">
            <li><a class="btn btn-outline" href="{{ asset('downloads/JETSI_Template.docx') }}" download><span class="material-icons" aria-hidden="true">description</span> JETSI manuscript template</a></li>
            <li><a class="btn btn-outline" href="{{ asset('downloads/JETSI_Cover_Page.docx') }}" download><span class="material-icons" aria-hidden="true">description</span> JETSI cover page</a></li>
          </ul>
        </div>
      </div>
    </section>

    <section class="section guideline-section">
      <div class="container">
        <div class="col-md-8">
          @foreach ($sections as $s)
            <h4>{{ $s[0] }}</h4>
            @foreach ($s[1] as $para)
              <p>{{ $para }}</p>
            @endforeach
          @endforeach
        </div>
      </div>
    </section>
  </main>
@endsection
