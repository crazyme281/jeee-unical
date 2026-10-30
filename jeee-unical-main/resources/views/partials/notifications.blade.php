@if(session('status') && isset(session('status')['type']))
    <?php
            $status = session('status');
    ?>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div class="alert alert-{{ $status['type'] }}">
                <div class="container-fluid">
                    <div class="alert-icon">
                        <i class="material-icons">{{ $status['type'] }}</i>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true"><i class="material-icons">clear</i></span>
                    </button>
                    {{ $status['message'] }}
                </div>
            </div>
        </div>
    </div>
@endif