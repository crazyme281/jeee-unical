@extends('layouts.base')

@section('title', 'Edit Manuscript')
@section('manuscript.active', 'active')

@section('styles')
    <link href="{{ asset('/css/material-bootstrap-wizard.css') }}" rel="stylesheet" />
    {{--<link href="{{ asset('/vendor/selectize/dist/css/selectize.default.css') }}" rel="stylesheet" />--}}
    <link href="{{ asset('/vendor/selectize/dist/css/selectize.css') }}" rel="stylesheet" />
    <link href="{{ asset('/vendor/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <section class="">
        <div class="container">
            <div class="col-md-12">
                <div class="title">
                    <h3>
                        {{ $manuscript['title'] }}
                    </h3>
                </div>
            </div>

                <div class="alert alert-primary col-md-12">
                    <div class="container-fluid">
                        <div class="alert-icon">
                            <i class="material-icons">info_outline</i>
                        </div>
                        <b>Add Manuscript Information:</b> Please be sure to review the
                        <a href="{{ route('guideline') }}" class="btn-primary">Submission Guideline</a>
                        to the journal to ensure that your submission is eligible for publication!
                    </div>
                </div>

                <!--      Wizard container        -->
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="purple" id="wizard">
                        <form class="form" method="POST" action="{{ route('manuscripts.update', $manuscript['id']) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="wizard-header">
                                <h3 class="wizard-title">
                                    Article Information
                                </h3>
                            </div>
                            <div class="wizard-navigation">
                                <ul>
                                    <li><a href="#basic" data-toggle="tab">Basic</a></li>
                                    <li><a href="#authors" data-toggle="tab">Authors</a></li>
                                    <li><a href="#files" data-toggle="tab">Files</a></li>
                                    <li><a href="#confirmation" data-toggle="tab">Confirmation</a></li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane" id="basic">
                                    <div class="row">
                                        <h4 class="info-text"> Let's start with the basic details</h4>

                                        <div class="col-md-offset-1 col-md-10">
                                            <div class="form-group is-empty {{ $errors->has('title') ? ' has-error' : '' }}">
                                                <label for="title">Title</label>
                                                <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? $manuscript['title'] }}" autofocus required>
                                                <span class="material-input"></span>
                                                <small class="form-text text-muted">Please give a very descriptive title.</small>
                                                @if ($errors->has('title'))
                                                    <span class="help-block">
                                                            <strong>{{ $errors->first('title') }}</strong>
                                                        </span>
                                                @endif
                                            </div>

                                            <div class="form-group is-empty {{ $errors->has('abstract') ? ' has-error' : '' }}">
                                                <label for="abstract">Abstract</label>
                                                <textarea id="abstract" type="text" rows="10" class="form-control" name="abstract" placeholder="Type here..." required>{{ old('abstract') ?? $manuscript['abstract'] }}</textarea>
                                                <span class="material-input"></span>
                                                <small class="form-text text-muted">Minimum of 50 words.</small>
                                                @if ($errors->has('abstract'))
                                                    <span class="help-block">
                                                            <strong>{{ $errors->first('abstract') }}</strong>
                                                        </span>
                                                @endif
                                            </div>

                                            <div class="form-group is-empty {{ $errors->has('keywords') ? ' has-error' : '' }}">
                                                <label for="keywords">Keywords</label>
                                                <input id="keywords" type="text" class="form-control" name="keywords" value="{{ old('keywords') ?? $manuscript['keywords'] }}" required>
                                                <span class="material-input"></span>
                                                <small class="form-text text-muted">Enter keywords one at a time then type a comma (",") so it is highlighted. All keywords will be lowercase.</small>
                                                @if ($errors->has('keywords'))
                                                    <span class="help-block">
                                                            <strong>{{ $errors->first('keywords') }}</strong>
                                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane active" id="authors">
                                    <div class="row">
                                        <h4 class="info-text">Who are the contributors? </h4>
                                    </div>

                                    <div class="authors">
                                        @foreach($manuscript['authors'] as $author)
                                            <div data-index="{{ $loop->index }}" class="row author-entry">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group is-empty {{ $errors->has('authors[names]') ? ' has-error' : '' }}">
                                                            <label for="authors[{{ $loop->index }}][names]">Names</label>
                                                            <input type="text" class="form-control names" name="authors[{{ $loop->index }}][names]" value="{{ $author['names'] }}">
                                                            <span class="material-input"></span>
                                                            @if ($errors->has('authors[names]'))
                                                                <span class="help-block">
                                                        <strong>{{ $errors->first('authors[names]') }}</strong>
                                                    </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group is-empty {{ $errors->has('authors[email]') ? ' has-error' : '' }}">
                                                            <label for="authors[{{ $loop->index }}][email]">Email</label>
                                                            <input type="text" class="form-control email" name="authors[{{ $loop->index }}][email]" value="{{ $author['email'] }}">
                                                            <span class="material-input"></span>
                                                            @if ($errors->has('authors[email]'))
                                                                <span class="help-block">
                                                                <strong>{{ $errors->first('authors[email]') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-offset-1 col-md-10">
                                                    <div class="col-md-6">
                                                        <div class="form-group is-empty {{ $errors->has('authors') ? ' has-error' : '' }}">
                                                            <label for="authors[{{ $loop->index }}][institution]">Institution</label>
                                                            <input type="text" class="form-control institution" name="authors[{{ $loop->index }}][institution]" value="{{ $author['institution'] }}">
                                                            <span class="material-input"></span>
                                                            @if ($errors->has('authors'))
                                                                <span class="help-block">
                                                            <strong>{{ $errors->first('authors') }}</strong>
                                                        </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group is-empty {{ $errors->has('authors') ? ' has-error' : '' }}">
                                                            <label for="authors[{{ $loop->index }}][department]">Department</label>
                                                            <input type="text" class="form-control department" name="authors[{{ $loop->index }}][department]" value="{{ $author['department'] }}">
                                                            <span class="material-input"></span>
                                                            @if ($errors->has('authors'))
                                                                <span class="help-block">
                                                            <strong>{{ $errors->first('authors') }}</strong>
                                                        </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <a class="btn btn-danger btn-simple remove-author pull-right">Remove Author<div class="ripple-container"></div></a>
                                                    <hr/>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 pull-right">
                                            <a id="add-author" class="btn btn-primary btn-simple">Add another Author<div class="ripple-container"></div></a>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="files">
                                    <h4 class="info-text">Upload the article and the supporting files. </h4>

                                    <div class="col-md-offset-1 col-md-10">
                                        <div class="form-group is-empty {{ $errors->has('file') ? ' has-error' : '' }}">
                                            <h4>
                                                Primary Manuscript File
                                            </h4>

                                            <a href="{{ url('storage'.$manuscript['file']) }}"> {{ $manuscript['file'] }} </a>
                                        </div>

                                        <div class="files">
                                            <h4>
                                                Additional Files
                                            </h4>

                                            @if(!empty($manuscript['support_files']))
                                                @foreach($manuscript['support_files'] as $file)
                                                    <div data-index="0" class="row file-entry">
                                                        <div class="col-md-12">
                                                            <a href="{{ url('storage'.$file['path']) }}"> {{ $file['description'] }} </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            <div data-index="0" class="row file-entry">
                                                <div class="col-md-6">
                                                    <div class="form-group is-empty {{ $errors->has('support_files[0][description]') ? ' has-error' : '' }}">
                                                        <label for="names">Description</label>
                                                        <input type="text" class="form-control description" name="support_files[0][description]" value="{{ old('support_files[0][description]') ?? $manuscript['support_files[0][description'] }}">
                                                        <span class="material-input"></span>
                                                        <small class="form-text text-muted">E.g. Corrections, Figure 2: Cross-sectional of ...</small>
                                                        @if ($errors->has('support_files[0][description]'))
                                                            <span class="help-block">
                                                        <strong>{{ $errors->first('support_files[0][description]') }}</strong>
                                                    </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group is-empty {{ $errors->has('title') ? ' has-error' : '' }}">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <span class="btn btn-default btn-file">
                                                                <span class="fileinput-new">Attachement</span>
                                                                <span class="fileinput-exists">Change</span>
                                                                <input class="file" type="file" name="support_files[0][file]" accept=".pdf,.doc,.docx,.jpeg,.jpg,.xlsx,.xls">
                                                            </span>
                                                            <span class="fileinput-filename"></span>
                                                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                                        </div>

                                                        <span class="material-input"></span>
                                                        <small class="form-text text-muted">Only [.doc, .docx, .pdf, .gif, jpeg].</small>
                                                    </div>
                                                    <a class="btn btn-danger btn-simple remove-file">Remove File<div class="ripple-container"></div></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 pull-right">
                                                <a id="add-file" class="btn btn-primary btn-simple">Add another File<div class="ripple-container"></div></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane" id="confirmation">
                                    <div class="row">
                                        <h4 class="info-text">Confirm the article details. </h4>

                                        <div class="col-md-offset-1 col-md-10">
                                            <h4>Agreement</h4>

                                            <div class="checkbox">
                                                <label>
                                                    <input id="agree" type="checkbox">
                                                    I have reviewed the guidelines and I agree to the terms and conditions.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="pull-right">
                                    <input type='button' class='btn btn-next btn-fill btn-primary btn-wd' name='next' value='Next' />
                                    <input id="finish" type='submit' class='btn btn-finish btn-fill btn-primary btn-wd' value='Finish' disabled/>
                                </div>
                                <div class="pull-left">
                                    <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Previous' />
                                </div>
                                <div class="clearfix"></div>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- wizard container -->

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('/js/material-bootstrap-wizard.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/vendor/selectize/dist/js/standalone/selectize.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/vendor/jasny-bootstrap/js/jasny-bootstrap.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function(){
            $('#keywords').selectize({
                plugins: ['remove_button'],
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });

            // Manage Additional Authors
            $('#add-author').click(function(){
                var lastEntry = $('.author-entry:last');

                // Add remove-author and hr
                var newEntry = lastEntry.clone();
                newEntry.find('input').val('');

                // find the current index
                var index = lastEntry.data('index') + 1;
                newEntry.data('index', index);

                newEntry.find(".names").attr('name', `authors[${index}][names]`);
                newEntry.find(".email").attr('name', `authors[${index}][email]`);
                newEntry.find(".institution").attr('name', `authors[${index}][institution]`);
                newEntry.find(".department").attr('name', `authors[${index}][department]`);

                newEntry.appendTo($('.authors'));
            });

            $(document).on('click', '.remove-author', function () {

                if($('.author-entry').length > 1){
                    var $this = $(this);
                    $this.parents('.author-entry').remove();
                }
            });


            // Manage Additional Files
            $('#add-file').click(function(){
                var lastEntry = $('.file-entry:last');

                // Add remove-file and hr
                var newEntry = lastEntry.clone();
                newEntry.find('input').val('');
                newEntry.find('.fileinput').removeClass('fileinput-exists').addClass('fileinput-new');
                newEntry.find('span.fileinput-exists').remove();
                newEntry.find('.fileinput-filename').html('');

                // find the current index
                var index = lastEntry.data('index') + 1;
                newEntry.data('index', index);

                newEntry.find(".description").attr('name', `support_files[${index}][description]`);
                newEntry.find(".file").attr('name', `support_files[${index}][file]`);

                newEntry.appendTo($('.files'));
            });

            $(document).on('click', '.remove-file', function () {

                if($('.file-entry').length > 1){
                    var $this = $(this);
                    $this.parents('.file-entry').remove();
                }
            });

            // Agreement
            $('#agree').click(function(){
                var $this = $(this);
                var agreed = $this.prop('checked');

                $('#finish').prop('disabled', !agreed);
            });
        });
    </script>
@endsection