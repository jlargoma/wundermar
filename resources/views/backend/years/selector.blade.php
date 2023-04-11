<div class="row push-10">
    <div class="@if(!$minimal) container @else col-md-12 @endif">
        <div class="col-xs-12 text-center">
            <div class="  title-year-selector @if(!$minimal) col-md-2 @else col-md-4 @endif @if(!$minimal)
            col-md-offset-3 @endif
            not-padding">
                <h2 style="margin: 0;" @if($minimal)class="text-left" @endif>
                    <b>@if($minimal) Año Actual @else Planning @endif</b>
                </h2>
            </div>
            <div class="@if(!$minimal) col-md-2 @else col-md-4 @endif">
                @include('backend.years._selector')
            </div>

        </div>
    </div>
</div>