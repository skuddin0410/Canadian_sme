@extends('layouts.admin')

@section('content')

<div class="container py-4">

    <h4 class="mb-4">All Poll Responses</h4>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Poll</th>
                        <th>Event</th>
                        <th>Total Questions</th>
                        <th>Total Answers</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($polls as $poll)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $poll->title }}</td>

                        <td>{{ $poll->event->title ?? '—' }}</td>

                        <td>{{ $poll->questions_count }}</td>

                        <td>{{ $poll->answers_count }}</td>

                        <td>{{ $poll->created_at->format('d M Y, H:i') }}</td>

                        <td>
                            <button
                                class="btn btn-sm btn-primary view-responses-btn"
                                data-id="{{ $poll->id }}">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No responses found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="responsesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Poll Responses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                Loading...
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.view-responses-btn').forEach(button => {

        button.addEventListener('click', function () {

            let pollId = this.getAttribute('data-id');

            fetch(`/admin/poll/${pollId}/show`) 
                .then(response => response.json())
                .then(data => {

                    if (!data.success) return;

                    let poll = data.poll;

                    let html = `
                        <h5>${poll.title}</h5>
                        <p><strong>Event:</strong> ${poll.event?.title ?? '-'}</p>
                        <hr>
                    `;

                    poll.questions.forEach(question => {

                        if (question.answers.length > 0) {

                            html += `<h6 class="mt-3">${question.question}</h6>`;

                            question.answers.forEach(answer => {

                                let answerText =
                                    answer.text_answer ??
                                    (answer.yes_no_answer !== null
                                        ? (answer.yes_no_answer ? 'Yes' : 'No')
                                        : answer.rating_answer
                                            ? '⭐ ' + answer.rating_answer
                                            : '-');

                                html += `
                                    <div class="border p-2 mb-2">
                                        <strong>User:</strong> ${answer.user?.name ?? 'Guest'} <br>
                                        <strong>Answer:</strong> ${answerText}
                                    </div>
                                `;
                            });
                        }
                    });

                    document.getElementById('modalContent').innerHTML = html;

                    let modal = new bootstrap.Modal(document.getElementById('responsesModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                });

        });

    });

});
</script>
@endsection