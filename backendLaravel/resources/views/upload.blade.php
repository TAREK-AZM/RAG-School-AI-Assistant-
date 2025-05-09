<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress {
            height: 20px;
            margin-bottom: 20px;
        }
        #upload-status {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload Document</div>
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Document Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="invalid-feedback" id="title-error"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category (Optional)</label>
                                <input type="text" class="form-control" id="category" name="category">
                                <div class="invalid-feedback" id="category-error"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="document" class="form-label">Document File</label>
                                <input type="file" multiple class="form-control" id="documents" name="documents[]" required>
                                <div class="invalid-feedback" id="document-error"></div>
                                <small class="text-muted">Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)</small>
                            </div>
                            
                            <div class="progress" style="display: none;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Upload Document</button>
                        </form>
                        
                        <div id="upload-status" class="alert" role="alert">
                            <h5 id="status-message"></h5>
                            <div id="document-details"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset error messages
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: '{{ route("school-assistant.upload") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = ((evt.loaded / evt.total) * 100);
                                $('.progress').show();
                                $('.progress-bar').width(percentComplete + '%');
                                $('.progress-bar').text(Math.round(percentComplete) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    beforeSend: function() {
                        $('#upload-status').hide();
                        $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
                    },
                    success: function(response) {
                        $('button[type="submit"]').prop('disabled', false).text('Upload Document');
                        $('#upload-status').removeClass('alert-danger').addClass('alert-success').show();
                        $('#status-message').text(response.message);
                        
                        let details = `
                            <div class="mt-3">
                                <strong>Title:</strong> ${response.document.title}<br>
                                <strong>Category:</strong> ${response.document.category}<br>
                                <strong>Status:</strong> Processing...
                            </div>
                        `;
                        
                        $('#document-details').html(details);
                        $('#uploadForm')[0].reset();
                        $('.progress').hide();
                    },
                    error: function(xhr) {
                        $('button[type="submit"]').prop('disabled', false).text('Upload Document');
                        $('.progress').hide();
                        
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            
                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').text('There were errors with your submission.');
                            $('#document-details').html('');
                        } else {
                            $('#upload-status').removeClass('alert-success').addClass('alert-danger').show();
                            $('#status-message').text('An error occurred while uploading your document.');
                            $('#document-details').html('Please try again later.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>