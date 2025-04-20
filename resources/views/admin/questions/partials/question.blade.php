<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h5>{{ $question->question }}</h5>
            <div class="btn-group">
                <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
                @if(!$question->is_final)
                    <a href="{{ route('admin.questions.create', ['parent_id' => $question->id]) }}" 
                       class="btn btn-sm btn-success">Add Sub-Question</a>
                @endif
            </div>
        </div>
        
        @if($question->is_final)
            <p class="mt-2"><strong>Answer:</strong> {{ $question->answer }}</p>
            <p class="text-muted">Input {{ $question->enable_input ? 'Enabled' : 'Disabled' }}</p>
        @endif

        @if($question->children->count() > 0)
            <div class="mt-3 ps-4">
                @foreach($question->children as $child)
                    @include('admin.questions.partials.question', ['question' => $child])
                @endforeach
            </div>
        @endif
    </div>
</div>