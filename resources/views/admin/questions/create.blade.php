<!-- resources/views/admin/questions/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Question</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @csrf
                    
                    <div class="card-body">
                        <!-- Parent Question Selection Section -->
                        <div class="form-group">
                            <label for="parent_id">Parent Question</label>
                            <!-- resources/views/admin/questions/create.blade.php -->
                            <select name="parent_id" id="parent_id" class="form-control select2">
                                <option value="">-- Root Question (No Parent) --</option>
                                @if($hasQuestions)
                                    @foreach($questions as $question)
                                        <option value="{{ $question->id }}" 
                                            {{ old('parent_id', $selectedParent) == $question->id ? 'selected' : '' }}>
                                            {{ $question->question }}
                                            @if($question->parent)
                                                (Child of: {{ $question->parent->question }})
                                            @endif
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No questions available yet</option>
                                @endif
                            </select>
                            
                            @if(!$hasQuestions)
                                <small class="text-muted">This will be created as a root question</small>
                            @endif
                        </div>

                        <!-- Question Field -->
                        <div class="form-group py-4">
                            <label for="question">Question Text *</label>
                            <textarea name="question" id="question" class="form-control" rows="2" required>{{ old('question') }}</textarea>
                        </div>

                        <!-- Answer Field -->
                        {{-- <div class="form-group">
                            <label for="answer">Answer (Leave empty if this leads to more questions)</label>
                            <textarea name="answer" id="answer" class="form-control" rows="3">{{ old('answer') }}</textarea>
                        </div> --}}

                        <div class="form-group my-4">
                            <label for="answer_type">Answer Type (Leave empty if this leads to more questions)</label>
                            <select name="answer_type" id="answer_type" class="form-control">
                                <option value="">No Answer</option>
                                <option value="simple" {{ old('answer_type') == 'simple' ? 'selected' : '' }}>Simple Text</option>
                                <option value="rich_text" {{ old('answer_type') == 'rich_text' ? 'selected' : '' }}>Rich Text Editor</option>
                                <option value="file" {{ old('answer_type') == 'file' ? 'selected' : '' }}>File Upload</option>
                                <option value="youtube" {{ old('answer_type') == 'youtube' ? 'selected' : '' }}>YouTube Video</option>
                            </select>
                        </div>
                        
                        <!-- Add this after the answer_type select field -->
                        <div id="answer_text_container" class="answer-input-container">
                            <textarea name="answer" id="answer" class="form-control" rows="3">{{ old('answer') }}</textarea>
                        </div>
                        
                        <div id="answer_editor_container" class="answer-input-container" style="display: none;">
                            <textarea name="answer_rich_text" id="answer_rich_text" class="form-control">{{ old('answer_rich_text') }}</textarea>
                        </div>
                        
                        <div id="answer_file_container" class="answer-input-container" style="display: none;">
                            <input type="file" name="answer_file" id="answer_file" class="form-control">
                            <small class="text-muted">Allowed: images (png,jpg,gif), docs, pdf, ppt, video files</small>
                        </div>
                        
                        <div id="answer_youtube_container" class="answer-input-container" style="display: none;">
                            <input type="url" name="answer_youtube" id="answer_youtube" class="form-control" placeholder="Enter YouTube URL" value="{{ old('answer_youtube') }}">
                        </div>

                        <!-- Options Toggle -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_final" name="is_final" value="1" {{ old('is_final') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_final">Final Question</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_input" name="enable_input" value="1" {{ old('enable_input') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_input">Enable User Input Field</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#parent_id').select2({
            placeholder: "Select parent question",
            allowClear: true
        });

        // Toggle logic
        $('#is_final').change(function() {
            if($(this).is(':checked')) {
                $('#enable_input').prop('checked', false);
            }
        });

        $('#enable_input').change(function() {
            if($(this).is(':checked')) {
                $('#is_final').prop('checked', false);
            }
        });
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('dist/ckeditor/ckeditor.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#parent_id').select2({
            placeholder: "Select parent question",
            allowClear: true
        });

        // Initialize CKEditor (but keep it hidden initially)
        let editor = CKEDITOR.replace('answer_rich_text');
        
        // Handle answer type changes
        $('#answer_type').change(function() {
            $('.answer-input-container').hide();
            
            switch($(this).val()) {
                case 'simple':
                    $('#answer_text_container').show();
                    break;
                case 'rich_text':
                    $('#answer_editor_container').show();
                    break;
                case 'file':
                    $('#answer_file_container').show();
                    break;
                case 'youtube':
                    $('#answer_youtube_container').show();
                    break;
            }
        }).trigger('change');

        // Toggle logic
        $('#is_final').change(function() {
            if($(this).is(':checked')) {
                $('#enable_input').prop('checked', false);
            }
        });

        $('#enable_input').change(function() {
            if($(this).is(':checked')) {
                $('#is_final').prop('checked', false);
            }
        });
    });
</script>
@endpush