@extends('layouts.app')
@section('page_title')
    {{(!empty($page_title) && isset($page_title)) ? $page_title : ''}}
@endsection
@push('head-scripts')
    <link href="{{ asset('common/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('common/select2/dist/css/select2-bootstrap5.min.css') }}" rel="stylesheet"/>
@endpush
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            {{-- Start: Page Content --}}
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">{{(!empty($p_title) && isset($p_title)) ? $p_title : ''}}</h4>
                    <div class="small text-medium-emphasis">{{(!empty($p_summary) && isset($p_summary)) ? $p_summary : ''}}</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">

                    <a href="{{(!empty($url) && isset($url)) ? $url : ''}}" class="btn btn-sm btn-primary">{{(!empty($url_text) && isset($url_text)) ? $url_text : ''}}</a>

                </div>
            </div>
            <hr>
            {{-- Start: Form --}}
            <form method="{{$method}}" action="{{$action}}" enctype="{{$enctype}}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="first_name">First Name</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name"  placeholder="First Name" value="{{(!empty($data->first_name) && isset($data->first_name)) ? $data->first_name : old('first_name')}}">
                    @error('first_name')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" id="last_name"  placeholder="Last Name" value="{{(!empty($data->last_name) && isset($data->last_name)) ? $data->last_name : old('last_name')}}">
                    @error('last_name')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Email" value="{{(!empty($data->email) && isset($data->email)) ? $data->email : old('email')}}">
                    @error('email')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="phone">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="Phone" value="{{(!empty($data->phone) && isset($data->phone)) ? $data->phone : old('phone')}}">
                    @error('phone')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="company_id">Company *</label>
                    <select class="select2-options-company-id form-control @error('company_id') is-invalid @enderror" name="company_id"></select>
                    @error('company_id')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <button type="submit" class="btn btn-sm btn-success">Submit</button>
            </form>
            {{-- End: Form --}}

            {{-- Modal --}}
            <div class="modal fade bd-example-modal-lg imageCropImg" id="model" tabindex="-1" role="dialog"
                 aria-labelledby="cropperModalLabel " aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header py-1 m-0 px-3 bg-dark-grey">
                            <h5 class="modal-title text-white fw-bold " id="cropperModal">Logo</h5>
                            <button type="button" class="close btn-close" id="reset-image-img" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0 m-0">
                            <div class="img-container">
                                <div class="row pe-4">
                                    <div class="col-md-12">
                                        <img class="cropper-image" id="previewImageImg" src="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-2 m-0">
                            <button type="button" class="btn  btn-sm bg-dark-green text-white crop" id="cropImageImg">Crop
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End: Modal --}}

            {{-- Page Description : Start --}}
            @if(!empty($p_description) && isset($p_description))
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 mb-sm-2 mb-0">
                            <p>{{(!empty($p_description) && isset($p_description)) ? $p_description : ''}}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Page Description : End --}}
            {{-- End: Page Content --}}
        </div>
    </div>
@endsection
@push('footer-scripts')
    <script src="{{ asset('common/select2/dist/js/select2.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            //Select Company
            let company=[{
                id: "{{$company['company_id']}}",
                text: "{{$company['company_name']}}",
            }];
            $(".select2-options-company-id").select2({
                data: company,
                theme: "bootstrap5",
                placeholder: 'Select Company',
            });
            $('.select2-options-company-id').select2({
                theme: "bootstrap5",
                placeholder: 'Select Company',
                allowClear: true,
                ajax: {
                    url: '{{route('get.employee-company-select')}}',
                    dataType: 'json',
                    delay: 250,
                    type: 'GET',
                    data: function (params){
                        var query = {
                            q: params.term,
                            type: 'public',
                            _token: '{{csrf_token()}}'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    id: item.company_id,
                                    text: item.company_name
                                }
                            })
                        };
                    },
                    cache: true
                }
            }).trigger('change.select2')
        })
    </script>

@endpush
