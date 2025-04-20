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

                <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
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
                        <div class="form-group">
                            <label for="answer">Answer (Leave empty if this leads to more questions)</label>
                            <textarea name="answer" id="answer" class="form-control" rows="3">{{ old('answer', $question->answer) }}</textarea>
                        </div>

                        <!-- Options Toggle -->
                        <div class="row">
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