@extends('layouts.jetsi')

@section('title', 'Submission Guideline')

@section('content')
  <section class="page-head">
    <div class="container">
      <h1>INSTRUCTIONS FOR CONTRIBUTORS TO GLOBAL JOURNAL SERIES AND BACHUDO PUBLISHERS</h1>
    </div>
  </section>

  <main id="main" class="guideline-page">
    @if(isset($current_issue))
    <div class="section guideline-ad">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    @include('partials.issue_advert', ['issue' => $current_issue])
                </div>
            </div>
        </div>
    </div>
    @endif

    <section class="section guideline-section">
        <div class="container">
            <div class="col-md-8">
                <p>
                

Global Journal Series Publishes original contributions in the areas indicated (no. 1 to 5). Review articles, Short- Communications, and Book Reviews are also welcome. Articles to be published should not exceed 10 printed pages. Authors are asked to contribute page charges at the galley proof stage. Articles should be sent to the Editor-In-Chief and Managing Editor. An assessment fee of N5000 (Five Thousand Naira) or (US$25) should accompany all articles submitted. Articles not accompanied by the assessment fee shall not be processed. If you want to send your article through the internet please use Microsoft word and send an Assessment fee of USD25 through Western Union. Money Transfer or pay into the Global Journal account.



                </p>
                <p>
                    
                    
                </p>
            </div>
        </div>
    </section>

    <section class="section guideline-section">
        <div class="container">
            <div class="col-md-8">
                <div class="title">
                    <h3>
                    The Journals in Global Journal Series are:
                    </h3>
                </div>

                <p>
                Global Journal of Pure and Applied Sciences
Global Journal of Agricultural Sciences
Global Journal of Educational Research
Global Journal of Social Sciences
Global Journal of Geological Sciences

                </p>
            </div>
        </div>
    </section>

    <section class="section guideline-section">
        <div class="container">
            <div class="col-md-8">
                <div class="title">
                    <h3>
                    TEXT
                    </h3>
                </div>

                <p>
                
Articles should be type-written with double line spacing and generous margins on the left side. All pages including figures, tables and references should be numbered. Use single column in typing your manuscript.
The first page should include a title, the name(s) of the authors(s) and their affiliations, and postal addresses. The title should be concise and not underlined. Authors are advised to spell out one of their given names rather than precede surnames with initials only. This facilitates identification in indexing and abstracting. It is the responsibility of the authors to secure the rights of reproduction of any material that has already been published elsewhere. In all cases, the S.I. units and metric systems only are acceptable. For mathematical symbols, all characters available on a computer must be typewritten in the text as well as in the equations and use latex or Amstex or MS- word.

                </p>
            </div>
        </div>
    </section>

    <section class="section guideline-section">
        <div class="container">
            <div class="col-md-8">
                <div class="title">
                    <h3>
                    ABSTRACT
                    </h3>
                </div>

                <p>
                    -- There must be an abstract not exceeding 300 words. It should be suitable 
for separate publication in an abstract journal. The Abstract should include 
the main results of research and should not be a mere table of contents. 
Provide no more than five key-words under the abstract.


                </p>
                <p>
                    -- Each of the following sections should begin on a
                    new page: title page, abstract, introduction, materials and methods, ethics, statics, results, discussion,
                    references, tables, legends to figure(s) and illustration(s).
                </p>
                <p>
                    -- Manuscripts should be sent to the Editor by email or uploaded on the journal website.
                </p>

                <p>
                    <span class="text-primary">Email</span>: globaljournalseries@gmail.com
                </p>

                <p>
                    <span class="text-primary">Website</span>: <a href="{{ route('home') }}">www.globaljournalseries.com.ng</a>
                </p>
            </div>

            <div class="col-md-8">
                <h4>
                    Title Page
                </h4>
                <p>
                    This should include the title of the article, names, initials and qualifications of each author; the department
                    and institution where the work was done; name and address of author to whom correspondence should
                    be addressed; Journal of Geological Science present addressees) of the authors(s) if different from that at
                    which the work was done; key words( not more than six words); and running title. Use of abbreviations
                    should be avoided in the title and abstract. The role of each co-author in the publication should be written
                    in a structured format. It should include background, methods, results and conclusion.
                </p>
                <br>

                <h4>
                    Introduction
                </h4>
                <p>
                    The objective of the study or review should be stated under this.
                </p>
                <br>

                <h4>
                    Methods
                </h4>
                <p>
                    The patients and materials used should be described. The nature of the study whether
                retrospective, prospective etc and any other methods used should be stated. For reviews, this should
                state the date sources and mode of data extraction.
                </p>
                <br>

                <h4>
                    Result
                </h4>
                <p>
                    This should state the main findings of the study.
                </p>
                <br>

                <h4>
                    Conclusion
                </h4>
                <p>
                    The main conclusions of the study should be stated.
                </p>
                <br>

                <h4>
                    Tables and illustrations
                </h4>
                <p>
                    Tables should be typed and double-spaced on separate sheet of paper, submitted in triplicate and
                    numbered consecutively with Roman numerals. Their approximate positions in the text should be
                    indicated. All illustrations are to be considered as figure, submitted in triplicate and numbered
                    consecutively with Arabic numerals. Figures and photographs should be lightly numbered on the back
                    and top indicated ad name(s) of author(s) written. Legend to each figure should be typed on a separate
                    sheet of paper. Photographs should conceal the subject&#39;s identify or sent with written consent of the
                    subject. Figures should be submitted as unmounted and untrimmed prints in a protective envelope. If an
                    illustration has been published previously, full credit to the original source must be given the legend.
                    Author(s) will be requested to pay for coloured illustration.
                </p>
                <br>

                <h4>
                REFERENCES
                </h4>
                <p>
                In the text references to the literature cited should list the author’s name, 
year of publication, and if possible specific page numbers e.g.  (Okpara, et al., 2021).
 At the end of text all literature cited should be listed alphabetically by authors and 
chronologically under each author. This section should be given the subheading 
“REFERENCES”. The style of citation is as shown below and References should no
t be numbered in the text and when listing them.

                </p>
                <br>
            </div>
        </div>
    </section>

    @if(isset($current_issue))
    <div class="section guideline-ad">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    @include('partials.issue_advert', ['issue' => $current_issue])
                </div>
            </div>
        </div>
    </div>
    @endif
  </main>
@endsection