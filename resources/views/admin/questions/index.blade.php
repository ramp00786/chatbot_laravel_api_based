@extends('layouts.app')

@section('title', 'Manage Questions')

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .subquestion-count {
        cursor: pointer;
        color: #3490dc;
        text-decoration: underline;
    }
    .subquestion-count:hover {
        color: #1c68c5;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Questions</h5>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">Add Question</a>
        </div>
    </div>
    <div class="card-body">
        <table id="questions-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Parent</th>
                    <th>Sub Questions</th>
                    <th>Has Input</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $index => $question)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $question->question }}</td>
                    <td>{{ $question->answer ?? '-' }}</td>
                    <td>{{ $question->parent->question ?? '-' }}</td>
                    <td>
                        @if($question->children_count > 0)
                            <span class="subquestion-count" 
                                  data-question-id="{{ $question->id }}"
                                  data-question-text="{{ $question->question }}">
                                {{ $question->children_count }}
                            </span>
                        @else
                            0
                        @endif
                    </td>
                    <td>{{ $question->enable_input ? 'Yes':'No' }}</td>
                    <td>{{ $question->created_at->format('Y-m-d H:i') }}</td>
                    <td nowrap>
                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.questions.create', ['parent_id' => $question->id]) }}" 
                           class="btn btn-sm btn-outline-success" 
                           title="Add Sub Question">
                            <i class="fas fa-plus-circle"></i>
                        </a>
                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#questions-table').DataTable({
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [3, 5] } // Disable sorting for sub-questions and actions columns
            ]
        });

        // Handle click on subquestion count
        $(document).on('click', '.subquestion-count', function() {
            const questionId = $(this).data('question-id');
            const questionText = $(this).data('question-text');
            
            // AJAX request to get sub-questions
            $.get(`/admin/questions/${questionId}/children`, function(data) {
                if (data.length > 0) {
                    let html = `<h5>Sub-questions of: "${questionText}"</h5><ul>`;
                    
                    data.forEach(function(subQuestion) {
                        html += `<li>${subQuestion.question} 
                                <small>(Created: ${new Date(subQuestion.created_at).toLocaleDateString()})</small>
                                </li>`;
                    });
                    
                    html += '</ul>';
                    
                    Swal.fire({
                        title: 'Sub Questions',
                        html: html,
                        confirmButtonText: 'Close',
                        width: '600px'
                    });
                } else {
                    Swal.fire({
                        title: 'No Sub Questions',
                        text: 'This question has no sub-questions',
                        icon: 'info'
                    });
                }
            }).fail(function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load sub-questions',
                    icon: 'error'
                });
            });
        });
    });
</script>
@endpush