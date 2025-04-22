<!-- resources/views/question.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>RAG Question Answering</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Ask a Question</h1>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form id="questionForm">
                            <div class="mb-3">
                                <label for="question" class="form-label">Question:</label>
                                <input type="text" class="form-control" id="question" name="question" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Ask</button>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4" id="answerCard" style="display: none;">
                    <div class="card-body">
                        <h5 class="card-title">Answer</h5>
                        <p id="answerText"></p>
                        <h6 class="mt-3">Sources:</h6>
                        <div id="sources"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#questionForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("school-assistant.ask") }}',
                    type: 'POST',
                    data: {
                        question: $('#question').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#answerCard').hide();
                    },
                    success: function(response) {
                        $('#answerText').text(response.answer);
                        
                        let sourcesHtml = '';
                        response.sources?.forEach(function(source) {
                            sourcesHtml += `<div class="card mb-2">
                                <div class="card-body">
                                    <h6>${source.document_title}</h6>
                                    <p>${source.content}</p>
                                </div>
                            </div>`;
                        });
                        $('#sources').html(sourcesHtml);
                        
                        $('#answerCard').show();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        alert('An error occurred while processing your question.');
                    }
                });
            });
        });
    </script>
</body>
</html>