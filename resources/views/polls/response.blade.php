@extends('layouts.admin')

@section('content')

<div class="container py-4">

    <h4 class="mb-4">
        Responses for: <strong>{{ $poll->title }}</strong>
    </h4>

    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answers as $answer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $answer->user->name ?? 'Guest' }}</td>
                                <td>{{ $answer->question->question }}</td>
                                <td>
                                    @if($answer->text_answer)
                                        {{ $answer->text_answer }}
                                    @elseif(!is_null($answer->yes_no_answer))
                                        {{ $answer->yes_no_answer ? 'Yes' : 'No' }}
                                    @elseif($answer->rating_answer)
                                        ⭐ {{ $answer->rating_answer }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $answer->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    No responses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if($answers->hasPages())
            <div class="card-footer">
                {{ $answers->links() }}
            </div>
        @endif
    </div>

</div>

@endsection