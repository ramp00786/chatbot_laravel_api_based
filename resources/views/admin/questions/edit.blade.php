<!-- resources/views/admin/questions/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Question</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <!-- Parent Question Selection -->
                        <div class="form-group">
                            <label for="parent_id">Parent Question</label>
                            <select name="parent_id" id="parent_id" class="form-control select2">
                                <option value="">-- Root Question (No Parent) --</option>
                                @foreach($questions as $q)
                                    @if($q->id !== $question->id) <!-- Prevent self-parenting -->
                                    <option value="{{ $q->id }}" 
                                        {{ $q->id == $question->parent_id ? 'selected' : '' }}
                                        {{ $question->isDescendantOf($q) ? 'disabled' : '' }}>
                                        {{ $q->question }}
                                        @if($q->parent)
                                            (Child of: {{ $q->parent->question }})
                                        @endif
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Question Field -->
                        <div class="form-group">
                            <label for="question">Question Text *</label>
                            <textarea name="question" id="question" class="form-control" rows="2" required>{{ old('question', $question->question) }}</textarea>
                            @error('question')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Answer Field -->
                        {{-- <div class="form-group">
                            <label for="answer">Answer (Leave empty if this leads to more questions)</label>
                            <textarea name="answer" id="answer" class="form-control" rows="3">{{ old('answer', $question->answer) }}</textarea>
                        </div> --}}

                        <!-- Add similar answer type handling to the edit form -->
                        <div class="form-group my-4">
                            <label for="answer_type">Answer Type (Leave empty if this leads to more questions)</label>
                            <select name="answer_type" id="answer_type" class="form-control">
                                <option value="">No Answer</option>
                                <option value="simple" {{ old('answer_type', $question->answer_type) == 'simple' ? 'selected' : '' }}>Simple Text</option>
                                <option value="rich_text" {{ old('answer_type', $question->answer_type) == 'rich_text' ? 'selected' : '' }}>Rich Text Editor</option>
                                <option value="file" {{ old('answer_type', $question->answer_type) == 'file' ? 'selected' : '' }}>File Upload</option>
                                <option value="youtube" {{ old('answer_type', $question->answer_type) == 'youtube' ? 'selected' : '' }}>YouTube Video</option>
                            </select>
                        </div>

                        <div id="answer_text_container" class="answer-input-container">
                            <textarea name="answer" id="answer" class="form-control" rows="3">{{ old('answer', $question->answer) }}</textarea>
                        </div>

                        <div id="answer_editor_container" class="answer-input-container" style="display: none;">
                            <textarea name="answer_rich_text" id="answer_rich_text" class="form-control">{{ old('answer_rich_text', $question->answer_type === 'rich_text' ? $question->answer : '') }}</textarea>
                        </div>

                        <div id="answer_file_container" class="answer-input-container" style="display: none;">
                            @if($question->answer_type === 'file' && $question->answer)
                            <div class="mb-2">
                                <p>Current file: {{ json_decode($question->answer_data)->original_name }}</p>
                                <a href="{{ asset('storage/'.$question->answer) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                            @endif
                            <input type="file" name="answer_file" id="answer_file" class="form-control-file">
                            <small class="text-muted">Allowed: images (png,jpg,gif), docs, pdf, ppt, video files</small>
                        </div>

                        <div id="answer_youtube_container" class="answer-input-container" style="display: none;">
                            <input type="url" name="answer_youtube" id="answer_youtube" class="form-control" 
                                placeholder="Enter YouTube URL" 
                                value="{{ old('answer_youtube', $question->answer_type === 'youtube' ? json_decode($question->answer_data)->url : '') }}">
                            @if($question->answer_type === 'youtube' && $question->answer)
                            <div class="mt-2">
                                <iframe width="100%" height="200" src="https://www.youtube.com/embed/{{ $question->answer }}" frameborder="0" allowfullscreen></iframe>
                            </div>
                            @endif
                        </div>

                        <!-- Options Toggle -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_final" name="is_final" value="1" 
                                            {{ old('is_final', $question->is_final) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_final">Final Question</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_input" name="enable_input" value="1" 
                                            {{ old('enable_input', $question->enable_input) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_input">Enable User Input</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Question
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
    .select2-container--default .select2-results__option[aria-disabled=true] {
        color: #999;
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

        // Prevent circular references
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