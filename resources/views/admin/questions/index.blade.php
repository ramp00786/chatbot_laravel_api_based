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
    
    /* Responsive table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Answer display styles */
    .answer-preview {
        cursor: pointer;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .answer-preview img {
        max-width: 50px;
        max-height: 50px;
        object-fit: cover;
    }
    
    .file-icon {
        font-size: 2rem;
        margin-right: 5px;
    }
    
    .pdf-icon { color: #e74c3c; }
    .doc-icon { color: #2c3e50; }
    .video-icon { color: #9b59b6; }
    .image-icon { color: #3498db; }
    .youtube-icon { color: #ff0000; }
    
    /* Modal styles */
    .modal-fullscreen {
        max-width: 100%;
        margin: 0;
    }
    
    .modal-fullscreen .modal-content {
        height: 100vh;
    }
    
    /* Mobile view adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 0.5rem;
        }
        
        .table td, .table th {
            padding: 0.5rem;
        }
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
        <div class="table-responsive">
            <table id="questions-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Parent</th>
                        <th>Sub Qs</th>
                        <th>Input</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $index => $question)
                    @php
                        $answerData = $question->answer_data ? json_decode($question->answer_data) : null;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $question->question }}</td>
                        <td>
                            <div class="answer-preview" 
                                 data-question-id="{{ $question->id }}"
                                 data-answer-type="{{ $question->answer_type }}"
                                 >
                                @if($question->answer)
                                    @switch($question->answer_type)
                                        @case('rich_text')
                                            <span>{!! Str::limit(strip_tags($question->answer), 50) !!}</span>
                                            @break
                                        @case('file')
                                            @if($answerData && $answerData->mime_type)
                                                @if(str_starts_with($answerData->mime_type, 'image/'))
                                                <img src="{{ route('file.view', basename($question->answer)) }}" alt="Image preview" class="img-thumbnail">
                                                @elseif(str_starts_with($answerData->mime_type, 'application/pdf'))
                                                    <i class="fas fa-file-pdf file-icon pdf-icon"></i>
                                                    <small>PDF File</small>
                                                @elseif(str_starts_with($answerData->mime_type, 'video/'))
                                                    <i class="fas fa-file-video file-icon video-icon"></i>
                                                    <small>Video File</small>
                                                @elseif(str_starts_with($answerData->mime_type, 'application/msword') || 
                                                        str_starts_with($answerData->mime_type, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
                                                    <i class="fas fa-file-word file-icon doc-icon"></i>
                                                    <small>Word Document</small>
                                                @elseif(str_starts_with($answerData->mime_type, 'application/vnd.ms-powerpoint') || 
                                                        str_starts_with($answerData->mime_type, 'application/vnd.openxmlformats-officedocument.presentationml.presentation'))
                                                    <i class="fas fa-file-powerpoint file-icon doc-icon"></i>
                                                    <small>PowerPoint</small>
                                                @else
                                                    <i class="fas fa-file-alt file-icon doc-icon"></i>
                                                    <small>{{ $answerData->original_name }}</small>
                                                @endif
                                            @endif
                                            @break
                                        @case('youtube')
                                            <i class="fab fa-youtube file-icon youtube-icon"></i>
                                            <small>YouTube Video</small>
                                            @break
                                        @default
                                            <span>{{ Str::limit($question->answer, 50) }}</span>
                                    @endswitch
                                @else
                                    <span>-</span>
                                @endif
                            </div>

                            
                        </td>
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
                        <td>{{ $question->created_at->format('Y-m-d') }}</td>
                        <td nowrap>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.questions.create', ['parent_id' => $question->id]) }}" 
                                   class="btn btn-outline-success" 
                                   title="Add Sub Question">
                                    <i class="fas fa-plus-circle"></i>
                                </a>
                                <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for answer content -->
<div class="modal fade" id="answerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Answer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalAnswerContent">
                <!-- Content will be inserted here by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="modalDownloadBtn" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
                <a href="#" id="modalViewBtn" class="btn btn-info" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen modal for PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfViewer" style="width:100%;height:100%;border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="pdfDownloadBtn" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#questions-table').DataTable({
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr'
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: -1 },
                { orderable: false, targets: [3, 5] }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search questions...",
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control');
            }
        });

        // Handle click on subquestion count
        $(document).on('click', '.subquestion-count', function() {
            const questionId = $(this).data('question-id');
            const questionText = $(this).data('question-text');
            
            $.get(`/admin/questions/${questionId}/children`, function(data) {
                if (data.length > 0) {
                    let html = `<h5>Sub-questions of: "${questionText}"</h5><ul class="list-group">`;
                    
                    data.forEach(function(subQuestion) {
                        html += `<li class="list-group-item">
                                <strong>${subQuestion.question}</strong>
                                <small class="text-muted d-block">Created: ${new Date(subQuestion.created_at).toLocaleDateString()}</small>
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

        // Handle click on answer preview
        // $(document).on('click', '.answer-preview', function() {
        //     const questionId = $(this).data('question-id');

        //     console.log(questionId);

        //     // Fetch data from laravel modal and update answerData with the column name answer_data then rest work should continue use async and wait and try and catch
           
        //     const answerData = $(this).data('answer-data') ? JSON.parse($(this).data('answer-data')) : null;
            
        //     const baseUrl = '{{ asset('storage') }}/';
        //     let modalContent = '';
        //     let showDownloadBtn = false;
        //     let showViewBtn = false;
        //     let downloadUrl = '';
        //     let viewUrl = '';
            
        //     // Hide buttons by default
        //     $('#modalDownloadBtn').addClass('d-none');
        //     $('#modalViewBtn').addClass('d-none');
            
        //     switch(answerType) {
        //         case 'rich_text':
        //             modalContent = `<div class="rich-text-content">${answerContent}</div>`;
        //             break;
                    
        //         case 'file':
        //             if (answerData) {
        //                 const fileUrl = baseUrl + answerContent;
                        
        //                 // First get the file type via AJAX
        //                 $.get(`/get-file-type/${questionId}`, function(fileInfo) {
        //                     if (fileInfo.file_type === 'image') {
        //                         modalContent = `
        //                             <div class="text-center">
        //                                 <img src="${fileUrl}" class="img-fluid" alt="File preview">
        //                             </div>`;
        //                         showDownloadBtn = true;
        //                         showViewBtn = true;
        //                     } 
        //                     else if (fileInfo.file_type === 'pdf') {
        //                         // Show PDF in fullscreen modal
        //                         $('#pdfViewer').attr('src', fileUrl);
        //                         $('#pdfDownloadBtn').attr('href', fileUrl);
        //                         $('#pdfModal').modal('show');
        //                         return;
        //                     }
        //                     else if (fileInfo.file_type === 'video') {
        //                         modalContent = `
        //                             <div class="ratio ratio-16x9">
        //                                 <video controls>
        //                                     <source src="${fileUrl}" type="${answerData.mime_type}">
        //                                     Your browser does not support the video tag.
        //                                 </video>
        //                             </div>`;
        //                         showDownloadBtn = true;
        //                     }
        //                     else {
        //                         // For other file types (doc, ppt, etc.)
        //                         modalContent = `
        //                             <div class="text-center">
        //                                 <i class="fas fa-file-alt fa-5x mb-3"></i>
        //                                 <p><strong>${answerData.original_name}</strong></p>
        //                                 <p>File type: ${answerData.mime_type}</p>
        //                             </div>`;
        //                         showDownloadBtn = true;
        //                     }
                            
        //                     // Set download URL
        //                     downloadUrl = fileUrl;
                            
        //                     // Set modal content and show appropriate buttons
        //                     $('#modalAnswerContent').html(modalContent);
                            
        //                     if (showDownloadBtn) {
        //                         $('#modalDownloadBtn')
        //                             .attr('href', downloadUrl)
        //                             .removeClass('d-none');
        //                     }
                            
        //                     if (showViewBtn) {
        //                         $('#modalViewBtn')
        //                             .attr('href', viewUrl)
        //                             .removeClass('d-none');
        //                     }
                            
        //                     $('#answerModal').modal('show');
        //                 });
        //                 return;
        //             }
        //             break;
                    
        //         case 'youtube':
        //             if (answerContent) {
        //                 modalContent = `
        //                     <div class="ratio ratio-16x9">
        //                         <iframe src="https://www.youtube.com/embed/${answerContent}" 
        //                             frameborder="0" 
        //                             allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        //                             allowfullscreen>
        //                         </iframe>
        //                     </div>`;
        //                 showViewBtn = true;
        //                 viewUrl = answerData.url;
        //             }
        //             break;
                    
        //         default:
        //             modalContent = `<p class="answer-full">${answerContent || 'No answer provided'}</p>`;
        //     }
            
        //     // Set modal content and show appropriate buttons
        //     $('#modalAnswerContent').html(modalContent);
            
        //     if (showDownloadBtn) {
        //         $('#modalDownloadBtn')
        //             .attr('href', downloadUrl)
        //             .removeClass('d-none');
        //     }
            
        //     if (showViewBtn) {
        //         $('#modalViewBtn')
        //             .attr('href', viewUrl)
        //             .removeClass('d-none');
        //     }
            
        //     $('#answerModal').modal('show');
        // });

        $(document).on('click', '.answer-preview', async function() {
            const questionId = $(this).data('question-id');
            const answerType = $(this).data('answer-type'); // You missed this earlier
            const baseUrl = '{{ asset('storage') }}/';

            let modalContent = '';
            let showDownloadBtn = false;
            let showViewBtn = false;
            let downloadUrl = '';
            let viewUrl = '';

            // Hide buttons by default
            $('#modalDownloadBtn').addClass('d-none');
            $('#modalViewBtn').addClass('d-none');

            try {
                // Fetch fresh answer_data from Laravel
                const response = await $.get(`{{ url('/get-answer-data') }}/${questionId}`);
                

                if(response.answer_data === '' || response.answer_data === null){
                    return false;
                }

                const answerData = response.answer_data ? JSON.parse(response.answer_data) : null;
                const answerContent = response.answer_content ?? '';

                if (!answerData && !answerContent) {
                    throw new Error('Answer data not found');
                }

                
                switch(answerType) {
                    case 'rich_text':
                        modalContent = `<div class="rich-text-content">${answerContent}</div>`;
                        break;

                    case 'file':
                        if (answerData) {
                            // const fileUrl = baseUrl + answerContent;

                            // Fetch file type
                            const fileInfo = await $.get(`/get-file-type/${questionId}`);

                            console.log(fileInfo.file_path);

                            

                            let fileUrl = '/file/view/' + fileInfo.file_path.split('/').pop();      // for preview (image, video, etc)
                            let downloadUrl = '/file/download/' + fileInfo.file_path.split('/').pop(); // for downloading

                            console.log(fileUrl);

                            if (fileInfo.file_type === 'image') {
                                modalContent = `
                                    <div class="text-center">
                                        <img src="${fileUrl}" class="img-fluid" alt="File preview">
                                    </div>`;
                                showDownloadBtn = true;
                                showViewBtn = true;
                                viewUrl = fileUrl;
                            } 
                            else if (fileInfo.file_type === 'pdf') {
                                // Open separate PDF modal
                                $('#pdfViewer').attr('src', fileUrl);
                                $('#pdfDownloadBtn').attr('href', fileUrl);
                                $('#pdfModal').modal('show');
                                return; // Stop here
                            } 
                            else if (fileInfo.file_type === 'video') {
                                modalContent = `
                                    <div class="ratio ratio-16x9">
                                        <video controls>
                                            <source src="${fileUrl}" type="${answerData.mime_type}">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>`;
                                showDownloadBtn = true;
                            } 
                            else {
                                modalContent = `
                                    <div class="text-center">
                                        <i class="fas fa-file-alt fa-5x mb-3"></i>
                                        <p><strong>${answerData.original_name}</strong></p>
                                        <p>File type: ${answerData.mime_type}</p>
                                    </div>`;
                                showDownloadBtn = true;
                            }

                            downloadUrl = fileUrl;
                        }
                        break;

                    case 'youtube':
                        if (answerContent) {
                            modalContent = `
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/${answerContent}" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                </div>`;
                            showViewBtn = true;
                            viewUrl = answerData?.url ?? '';
                        }
                        break;

                    default:
                        modalContent = `<p class="answer-full">${answerContent || 'No answer provided'}</p>`;
                }

                // Set modal content and show buttons
                $('#modalAnswerContent').html(modalContent);

                if (showDownloadBtn) {
                    $('#modalDownloadBtn')
                        .attr('href', downloadUrl)
                        .removeClass('d-none');
                }

                if (showViewBtn) {
                    $('#modalViewBtn')
                        .attr('href', viewUrl)
                        .removeClass('d-none');
                }

                $('#answerModal').modal('show');

            } catch (error) {
                console.log('Error loading answer preview:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong while loading the answer preview.',
                });
            }
        });

    });
</script>
@endpush