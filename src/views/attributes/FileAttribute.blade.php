<label class="form-group">
    <span class="form-label">
        {{ Form::label($name, $label) }}
    </span>
    <span class="form-field js-image-container">
        <span class="form-control" style="display: table; table-layout: fixed; width: 100%;">
            <span class="" style="display: table-cell; vertical-align: top; width: 50%;">
                <span class="media-wrapper" style="margin: 0 3px 0 0;">
                    <span class="media-wrapper-content">
                        <span class="media-wrapper-content-wrapper">
                            <span class="media-wrapper-content-wrapper-inner">
                                {{ Form::file($name, null, array('class' => 'form-field-content')) }}
                                <span class="form-group-highlight"></span>
                            </span>
                        </span>
                    </span>
                </span>
            </span>
            <span class="" style="display: table-cell; vertical-align: top; width: 50%;">
                <span class="media-wrapper" style="margin: 0 0 0 3px;">
                    @if ($value)
                    <span class="media-wrapper-content">
                        <span class="media-wrapper-content-wrapper">
                            <span class="media-wrapper-content-wrapper-inner">
                                {{ $value }}
                            </span>
                        </span>
                    </span>
                    <button class="btn btn-remove confirm-delete-dialog" data-callback="removeImage" data-name="{{ $name }}" style="height: 2.75em; line-height: 2.75em; padding: 0; position: absolute; right: 0; top: 0; width: 2.75em;" type="button">X</button>
                    @endif
                </span>
            </span>
        </span>
        {{--
        <span class="group paragraph section-end">
            <span style="display: block; width: 50%;">
                {{ Form::select($name . '_existing', $relatives) }}
            </span>
        </span>
        --}}
        @include('shapeshifter::layouts.helptext')
    </span>
</label>